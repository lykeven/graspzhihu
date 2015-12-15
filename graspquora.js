/**
 * Created by Administrator on 2015/5/14.
 */
var https = require("https");
var request=require("request");
var cheerio = require("cheerio");
var mysql = require('mysql');
var async = require('async');
var iconv = require('iconv-lite');
var zlib = require('zlib');

var gunzipStream = zlib.createGunzip();

var client = mysql.createConnection({
    user: 'sqluser',
    password: 'passwd'
});
var TEST_DATABASE = 'sqluser';
var TEST_TABLE= 'quoraquestion';
var TEST_TABLE2='quorapeople';
client.connect();
client.query("use " + TEST_DATABASE);

var querystring=require("querystring");
var contents=querystring.stringify({
    rememberme:"y"
});


var peoplelist =new Array();
var questionlist =new Array();
var questioninfo=new Array();
var questioninfo2=new Array();
var p= 0,q=0;
var pl= 1,ql=0;
var lp=5000,lq=5000;


function start()
{
    peoplelist[p++]=["Senia Sheydvasser","https://www.quora.com/Senia-Sheydvasser?share=1"];
    graspquestion("https://www.quora.com/Senia-Sheydvasser?share=1");
}


function download(url, callback)
{
    var req=https.request(url, function(res) {
        if(res.headers['content-encoding']!=undefined&&res.headers['content-encoding']['gzip']!=undefined)
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

        if(data=="") return;
    }).on("error", function() {
        return;
        callback(null);
    });
    req.write(contents);
    req.end();
}



function graspquestion(url)
{
    if(p>lp||q>lq)
        return ;

    download(url, function(data)
    {
        var $ = cheerio.load(data);
        //console.log(data);
        var username=$("title").text();
        var followeeslist=$("div.PagedList");
        followeeslist.find("div.pagedlist_item").each(function(i, e)
        {
            var question=$(e).find("div.feed_type_answer>div.feed_item_inner").find("div.QuestionText").text();
            var link=  $(e).find("div.feed_type_answer>div.feed_item_inner").find("div.QuestionText").find("a.question_link").attr("href");
            var time=  $(e).find("div.feed_type_answer>div.feed_item_inner").find("span.timestamp").text();
            if(question!=undefined&&link!=undefined&&time!=undefined)
            {
                var charCode=question.charCodeAt(0);
                if(charCode<=48 || charCode>128)
                    question=question.slice(1,question.length-1);
                questioninfo[q++]=[question,link,time];
                graspquestioninfo("https://www.quora.com"+link+"?share=1",q-1);
                //console.log("questioninfo["+q+"]:"+questioninfo[q-1]);

            }

        } );

    } );

}




function graspquestioninfo(url,index)
{
    if(p>lp||q>lq)
        return ;

    download(url, function(data)
    {
        var $ = cheerio.load(data);
        //console.log(data);
        var answerSum=$("div.answer_count").text().slice(0,-8);
        var voteSum= 0,commentSum=0;


        var followeeslist=$("div.PagedList");
        followeeslist.find("div.pagedlist_item").each(function(i, e)
        {
            var vote=$(e).find("span.answer_voters>span").first().text();
            var comment=$(e).find("a.view_comments>span.count").text();

            var people = $(e).find("div.author_info").find("span.feed_item_answer_user").find("a").first().text();
            var link= $(e).find("div.author_info").find("a").first().attr("href");
            if(people==""||people==undefined)
                people=link;

            if(link!=undefined&&people!=undefined&&link.search('#')==-1)
            {
                peoplelist[p++]=[people,link];
                console.log("peoplelist["+p+"]:"+peoplelist[p-1]);
            }
            if(vote) voteSum=parseInt(vote)+ parseInt(voteSum);

            if(comment[comment.length-1]=="+") comment=comment.slice(0,-1);
            if(comment) commentSum=parseInt(comment)+parseInt(commentSum);

        } );
        questioninfo2[index]=[answerSum,voteSum,commentSum];
        insertquestion(index);
        console.log("questioninfo2["+index+"]:"+questioninfo2[index]);
        for(pl;pl<p;pl++)
        {
            var u=peoplelist[pl][1];
            graspquestion("https://www.quora.com"+u+"?share=1");
            insertperson(pl);

        }

    } );

}


//插入用户信息
function insertperson(index)
{
    if(peoplelist[index]!=undefined)
    {
        console.log("number"+index+":people:"+peoplelist[index])
        client.query(
            'INSERT IGNORE INTO ' + TEST_TABLE2 + ' SET user=?,link=?', peoplelist[index],
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
        questionlist[index] = [questioninfo[index][0], questioninfo2[index][0], questioninfo2[index][1], questioninfo2[index][2], questioninfo[index][1], questioninfo[index][2]];
        console.log("number" + index + ":question:" + questionlist[index])
        client.query(
            'INSERT IGNORE INTO ' + TEST_TABLE + ' SET question=?,answerSum=?,voteSum=?,commentSum=?,link=?,time=?', questionlist[index],
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



//start();

exports.run = function()
{
    start();
};
