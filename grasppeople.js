var http = require("http");
var request=require("request");
var cheerio = require("cheerio");
var mysql = require('mysql');
var async = require('async');
var iconv = require('iconv-lite');
var zlib = require('zlib');

//压缩


//连接数据库
var client = mysql.createConnection({
    user: 'sqluser',
    password: 'passwd'
});
var TEST_DATABASE = 'sqluser';
var TEST_TABLE= 'zhihuquestion';
var TEST_TABLE2= 'zhihupeople';
client.connect();
client.query("use " + TEST_DATABASE);

//连接参数
var xs="";
var session=[];
var url = "http://www.zhihu.com/login";
var querystring=require("querystring");
var contents=querystring.stringify({
    _xsrf:xs,
    email:"email",
    password:"passwd",
    rememberme:"y"
});

//数组
var peoplelist =new Array();
var questionlist =new Array();
var questioninfo=new Array();
var questioninfo2=new Array();
var p= 0,q=0;
var pl= 1,ql=0;
var lp=10000,lq=1000;
var lt=70000;

var topiclist =["游戏","经济学","运动","互联网","艺术","阅读","美食","动漫","汽车","生活方式","教育","摄影",
    "历史","文化","旅行","职业发展","足球","篮球","投资","音乐","电影","法律","自然科学","设计",
    "创业","健康","商业","体育","生活","科技","化学","物理学","生物学","金融" ];



//登陆
function login(url)
{
    download(url, function(data) {
        if (data) {
            var $ = cheerio.load(data);
            //console.log(data);
            xs=$("div.view-signin>form.zu-side-login-box").find("input").first().attr("value");
            console.log(xs);
            savecookie();
        }
    });
}

//保存cookies
function savecookie()
{
    request(
        {
            url: 'http://www.zhihu.com/login',
            method: 'POST',
            encoding: 'utf8',
            headers: {
                ContentType: 'application/x-www-form-urlencoded'
            },
            form: {
                _xsrf:xs,
                email:"1402442862@qq.com",
                password:"hz376645513",
                rememberme:"y"
            }
        },
        function (err, res, body) {
            body = iconv.decode(body, "utf8");
            session = res.headers['set-cookie'];
            console.log(session);

            //console.log(body);
            if (body == 'ok') {
                callback({Result: true, Session: session});
            }
            else {
                // callback(false);
            }
            peoplelist[p++]=["123","http://www.zhihu.com/people/imike/followees"];
            downfollowees("http://www.zhihu.com/people/xiaodaoren/followees");
        }
    );

}


//下载
function download(url, callback)
{
    var req=http.request(url, function(res) {
        var gunzipStream = zlib.createGunzip();
        if(res.headers['content-encoding']!=undefined)
        {
            if(res.headers['content-encoding'].indexOf('gzip') != -1)
                res=res.pipe(gunzipStream);
        }

        var data = "";
        res.on('data', function (chunk) {
            data += chunk;
        });
        res.on("end", function() {
            callback(data);
        });
    }).on("error", function() {
        callback(null);
    });
    req.write(contents);
    req.end();
}


//下载关注的人
function downfollowees(url)
{
    request
    (
        {
            uri: url,
            encoding: null,
            headers: {
                Cookie: session
            }
        }, function (err, res, body) {
            if (err) {
                //callback('Server Error');
            }
            if(body=="") return;
            if(body==undefined) return;
            var gunzipStream = zlib.createGunzip();
            if(res.headers['content-encoding']!=undefined)
            {
                if(res.headers['content-encoding'].indexOf('gzip') != -1)
                    res=res.pipe(gunzipStream);
            }
            //var buffer = new Buffer('eJzT0yMAAGTvBe8=', 'base64');
            var result="";

            //console.log(body);
            zlib.unzip(body, function(err, body) {
                if (!err) {
                    //console.log(body.toString());
                    result=body.toString();
                    setTimeout(graspfolloweeslist(result),3000);

                }
            });
            body = iconv.decode(body, "utf8");
            //setTimeout(console.log("result:"+result+"end"),10000);
            //setTimeout(graspfolloweeslist(result),3000);
        }
    );
}

//下载关注者名单
function graspfolloweeslist(data)
{

    if(p>lp)
        return ;

    //console.log(data);
    var $ = cheerio.load(data);
    var followeeslist=$("div.zh-general-list");
    followeeslist.find("div.zm-profile-card").each(function(i, e)
    {
        var followees=$(e).find("h2.zm-list-content-title>a.zg-link").text();
        var lin=$(e).find("h2.zm-list-content-title>a.zg-link").attr("href");
        var link = $(e).find("div.zm-list-content-medium>h2").attr("href");
        var pic=  $(e).find("a.zm-item-link-avatar").find("img.zm-item-img-avatar").attr("src");
        //console.log("pic:"+pic);
        peoplelist[p++]=[followees,lin,pic];
        //console.log("number:"+p+"peoplelist:"+peoplelist[p-1]);

    });

    for(pl;pl<p;pl++)
    {
        var u=peoplelist[pl][1];
        insertperson(pl);
        //downloadpeoplequestion(u);
        downfollowees(u+"/followees");
    }
}



function downloadpeoplequestion(peoplequestionurl)
{
    download(peoplequestionurl, function(data) {
        if (data) {
            graspquestion(data);
        }
    });

}

//下载问题
function graspquestion(data)
{
    var $ = cheerio.load(data);

    var username=$("title").text();
    //console.log("username:"+username);

    var userquestionlist=$("div.zm-profile-section-list").last();
    var k=0;
    userquestionlist.find("div.zm-profile-section-item").each(function(i, e)
    {
        var time= $(e).find("span.zm-profile-setion-time").text();
        var question=$(e).find("a.question_link").text();
        var link = $(e).find("a.question_link").attr("href");
        if(time!=undefined&&question!=undefined&&link!=undefined)
        {
            downloadquestion(link,q);
            questioninfo2[q++]=[question,link,time];

            //console.log("questioninfo2["+q+"]:"+questioninfo2[q-1]);
        }
    });

}


function downloadquestion(questionurl,index)
{

    var url = "http://www.zhihu.com"+questionurl;
    console.log("questionurl["+index+"]:"+url);
    download(url, function(data) {
        if (data) {
            graspquestioninfo(data,index);
        }
    });
}


//下载问题信息
function graspquestioninfo(data,index)
{
    var $ = cheerio.load(data);
    var answerSum=$("div.zh-answers-title>h3").text().slice(0,-4).replace(/\s+/g, '');
    if(answerSum=="")
        answerSum=0;
    else
    {
        var charCode=answerSum.charCodeAt(0);
        if(!(charCode>0 && charCode<=128))
        {
            answerSum=answerSum.slice(4,10);
            answerSum=letterpro(answerSum);
        }
    }

    var voteSum=0;
    var commentSum=0;
    var answerlist=$("div.zm-item-answer");

    var topic;
    var topicl=$("div.zm-tag-editor-labels");
    topicl.find("a.zm-item-tag").each(function(i, e) {
        var temp= $(e).text().replace(/\s+/g, '');
        for(var i=0;i<topiclist.length;i++)
        {
            if(temp==topiclist[i])
            {
                topic=temp;
                break;
            }
        }
    });


    answerlist.each(function(i, e)
    {
        var vote= $(e).find("div.zm-votebar>button.up").find("span.count").text();
        var comment=$(e).find("div.zm-item-comment-el>div.zm-meta-panel").find("a.toggle-comment").text().replace(/\s+/g, '') ;

        if(vote) voteSum=parseInt(vote)+ parseInt(voteSum);

        var commentvalue=comment.slice(0,-3);
        var charCode=commentvalue.charCodeAt(0);
        if(charCode>0 && charCode<=128)
            commentSum+=parseInt(commentvalue);
    });

    var ind=parseInt(index);
    questioninfo[ind]=[answerSum,voteSum,commentSum,topic];
    console.log("questioninfo["+ind+"]:"+questioninfo[ind]);
    insertquestion(ind);
}

//字符串处理
function letterpro(string)
{
    var ans="";
    for(var i=0;i<string.length;i++)
    {
        var charCode=string.charCodeAt(i);
        if(charCode>0 && charCode<=128)
            ans=ans+string[i];
    }
    string=parseInt(ans);
    return string;
}

//插入用户信息
function insertperson(index)
{
    if(peoplelist[index]!=undefined)
    {
        console.log("number"+index+":people:"+peoplelist[index])
        client.query(
            'INSERT IGNORE INTO ' + TEST_TABLE2 + ' SET user=?,link=?,pic=?', peoplelist[index],
            function selectCb(err, results, fields) {
                if (err) {
                    throw err
                }
                //client.end();
            }
        );
    }
    delete peoplelist[index];
}


//插入问题信息
function insertquestion(index)
{
    if(questioninfo[index]!=undefined) {
        questionlist[index] = [questioninfo2[index][0], questioninfo[index][0], questioninfo[index][1], questioninfo[index][2], questioninfo2[index][1], questioninfo2[index][2],questioninfo[index][3]];
        console.log("number" + index + ":question:" + questionlist[index])
        client.query(
            'INSERT IGNORE INTO ' + TEST_TABLE + ' SET question=?,answerSum=?,voteSum=?,commentSum=?,link=?,time=?,topic=?', questionlist[index],
            function selectCb(err, results, fields) {
                if (err) {
                    throw err
                }
                //client.end();
            }
        );

        delete questioninfo[index];
        delete questioninfo2[index];
        delete questionlist[index];
    }
}



//login("http://www.zhihu.com/");

exports.run = function()
{
    login("http://www.zhihu.com/");
};
