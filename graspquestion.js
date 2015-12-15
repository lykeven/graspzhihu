/**
 * Created by Administrator on 2015/6/7.
 */
var http = require("http");
var request=require("request");
var cheerio = require("cheerio");
var mysql = require('mysql');
var async = require('async');
var iconv = require('iconv-lite');
var zlib = require('zlib');
var wordpress = require( "wordpress" );

//压缩

var xs="";
var session=[];
var querystring=require("querystring");
var contents=querystring.stringify({
    _xsrf:xs,
    email:"email",
    password:"passwd",
    rememberme:"y"
});



var nowDate=new Date();
nowDate=nowDate.toLocaleDateString().replace('-','_').replace('-','_');
console.log(nowDate);

var client = mysql.createConnection({
    user: 'sqluser',
    password: 'passwd'
});
var TEST_DATABASE = 'sqluser';
var BASE_PEOPLE="zhihupeople";
var BASE_QUESTION="zhihuquestion";
var TEST_PEOPLE="zhihupeople"+nowDate;
var TEST_QUESTION="zhihuquestion"+nowDate;
client.connect();
client.query("use " + TEST_DATABASE);

var topiclist =["游戏","经济学","运动","互联网","艺术","阅读","美食","动漫","汽车","生活方式","教育","摄影",
    "历史","文化","旅行","职业发展","足球","篮球","投资","音乐","电影","法律","自然科学","设计",
    "创业","健康","商业","体育","生活","科技","化学","物理学","生物学","金融" ];



var peoplelist=new Array();
var p=0;
function createtable()
{
    client.query(
        'create table ' + TEST_PEOPLE +
        ' ( user varchar(50) primary key,' +
        'link varchar(50),' +
        'pic text)'+
        'DEFAULT CHARSET=utf8',
        function selectCb(err, results, fields) {
            if (err) {
                throw err
            }
            //client.end();
        }
    );

    client.query(
        'create table ' + TEST_QUESTION +
        ' ( question varchar(50) primary key,' +
        'link varchar(50),' +
        'topic varchar(10),' +
        'ansuser varchar(50),' +
        'userlink varchar(50),' +
        'pic text,'+
        'voteSum int ,'+
        'anstext text)'+

        'DEFAULT CHARSET=utf8',
        function selectCb(err, results, fields) {
            if (err) {
                throw err
            }
            //client.end();
        }
    );
}


var wpclient = wordpress.createClient({
    url: "url",
    username: "user",
    password: "passed"

});


function download(url, callback) {
    http.get(url, function(res) {
        //console.log(res.headers);
        if(res.headers['content-encoding']!=undefined)
            if(res.headers['content-encoding'].indexOf('gzip') != -1)
            {
                var gunzipStream = zlib.createGunzip();
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
}

function downloadpeoplequestion(peoplequestionurl)
{

    download(peoplequestionurl, function(data) {
        if (data) {
            graspquestion(data);

        }
    });

}

function downloadquestion(question,questionurl)
{

    var url = "http://www.zhihu.com"+questionurl;

    download(url, function(data) {
        if (data) {
            var $ = cheerio.load(data);
            var voteSum=0;
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

                if(vote) voteSum=parseInt(vote);
                if(voteSum>500)
                {
                    var answertext= $(e).find("div.zm-editable-content").text();
                    if(answertext.length>=50) answertext=answertext.slice(0,50)+"...";
                    var answeruser=   $(e).find("div.answer-head").find("div.zm-item-answer-author-info>h3.zm-item-answer-author-wrap").
                        find("a").last().text();
                    var answeruserlink=  $(e).find("div.answer-head").find("div.zm-item-answer-author-info>h3.zm-item-answer-author-wrap").
                        find("a").last().attr("href");
                    var answeruserpic=$(e).find("div.answer-head").find("div.zm-item-answer-author-info>h3.zm-item-answer-author-wrap")
                        .find("a.zm-item-link-avatar>img").attr("src");
                    var a=[question , questionurl,topic,answeruser,answeruserlink,answeruserpic,voteSum,answertext,] ;
                    console.log("get "+a[0]);
                    //console.log(question+"\t"+questionurl+"\t"+topic+"\t"+answeruser+"\t"+answeruserlink+"\t"+answeruserpic+"\t"+voteSum+"\t"+answertext);
                    client.query(
                        'INSERT IGNORE INTO ' + TEST_QUESTION + ' SET question=?,link=?,topic=?,ansuser=?,userlink=?,pic=?,voteSum=?,anstext=?',a ,

                        function selectCb(err, results, fields) {
                            if (err) {
                                throw err
                            }
                            //client.end();
                        }
                    );
                    delete  a;
                }

            });


        }
    });
}


//下载问题
function graspquestion(data)
{
    var $ = cheerio.load(data);
    var title=$("title").text();
    var userquestionlist=$("div.zm-profile-section-list").last();
    userquestionlist.find("div.zm-profile-section-item").each(function(i, e)
    {
        var time= $(e).find("span.zm-profile-setion-time").text();
        var question=$(e).find("a.question_link").text();
        var link = $(e).find("a.question_link").attr("href");
        //console.log(title+"\t"+time+"\t"+question+"\t"+link);
        if(time!=undefined&&question!=undefined&&link!=undefined)
        {
            downloadquestion(question,link);
        }
    });

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


function updatedata()
{
    client.query(
        'select * from '+BASE_PEOPLE,
        function selectCb(err, results, fields) {
            if (err) {
                throw err
            }
            if(results)
            {
                console.log(results.length);

                for(var i = 0; i <results.length; i++)
                {
                    console.log("number:"+i+"%s\t%s", results[i].user , results[i].link);
                    peoplelist[p++]=results[i].link;

                    downloadpeoplequestion(results[i].link);
                }
            }
            //client.end();
        }
    );
}



//createtable();
//setTimeout(updatedata(),3000);



exports.run = function()
{
    //createtable();
    setTimeout(updatedata(),3000);
};