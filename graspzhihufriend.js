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
var p= 0,q=0;


//登陆
function login(url)
{
    download(url, function(data) {
        if (data) {
            var $ = cheerio.load(data);
            //console.log(data);
            xs=$("div.view-signin>form.zu-side-login-box").find("input").first().attr("value");
            //console.log(xs);
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
                email:"email",
                password:"passwd",
                rememberme:"y"
            }
        },
        function (err, res, body) {
            body = iconv.decode(body, "utf8");
            session = res.headers['set-cookie'];
            //console.log(session);

            //console.log(body);
            if (body == 'ok') {
                callback({Result: true, Session: session});
            }
            else {
                // callback(false);
            }

            var argv=process.argv[2];
            peoplelist[p++]=["123","http://www.zhihu.com/people/imike/followees"];
            downfollowees("http://www.zhihu.com/people/"+argv+"/followees");
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

            var gunzipStream = zlib.createGunzip();
            if(res.headers['content-encoding']!=undefined)
            {
                if(res.headers['content-encoding'].indexOf('gzip') != -1)
                    res=res.pipe(gunzipStream);
            }

            if(body=="") process.exit();
            if(body==undefined) process.exit();
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
            //setTimeout(graspfolloweeslist(body),3000);
        }
    );
}


//下载关注者名单

function graspfolloweeslist(data)
{

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
        console.log(followees);
        console.log(lin);
        console.log(pic);


        if(p>20)
            process.exit();

    });

    setTimeout(process.exit(),3000);


}



login("http://www.zhihu.com/");
setTimeout(savecookie(),1000);

