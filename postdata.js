/**
 * Created by Administrator on 2015/6/12.
 */
var mysql = require('mysql');
var wordpress = require( "wordpress" );

var nowDate=new Date();
nowDate=nowDate.toLocaleDateString().replace('-','_').replace('-','_');

var client = mysql.createConnection({
    user: 'sqluser',
    password: 'passwd'
});
var TEST_DATABASE = 'sqluser';
var TEST_QUESTION="zhihuquestion"+nowDate;
client.connect();
client.query("use " + TEST_DATABASE);

var topiclist =["游戏","经济学","运动","互联网","艺术","阅读","美食","动漫","汽车","生活方式","教育","摄影",
    "历史","文化","旅行","职业发展","足球","篮球","投资","音乐","电影","法律","自然科学","设计",
    "创业","健康","商业","体育","生活","科技","化学","物理学","生物学","金融" ];

var wpclient = wordpress.createClient({
    url: "http://127.0.0.2/wordpress/",
    username: "user",
    password: "passwd"

});



var co = "";
var sql="select question,link,ansuser,userlink,pic,anstext from "+TEST_QUESTION+"  order by voteSum DESC limit 20";

function getcon(){
    client.query(sql, function (err, res) {

        for(var i=0;i<20;i++)
        {
            co += "<h2>" +
                "<a href=\"" + "http://www.zhihu.com" +  res[i].link + "\">"+(i+1)+": " + res[i].question + "</a> <br>" +
                    //"<img src=\""+res[i].pic+"\"> </img>  <br>"+
                "<a href=\"" + "http://www.zhihu.com" +  res[i].userlink + "\">" + res[i].ansuser + "</a> <br>" +
                "<span text-overflow=ellipsis>"+res[i].anstext+"<a href=\"" + "http://www.zhihu.com"+res[i].link+"\"> 查看全部 </a>"+"</span>"+
                "</h2><br>";
        }
        co+="[wpfp-link]";			//添加加入收藏链接
        console.log(co);

        wpclient.newPost({
            title: nowDate+"精选",
            terms:{'category':[1]},
            content: co,
            status:"future"
        }, function( error, data ) {
            console.log( arguments );
        });
    });
}

function getcon2(topic){
    var con = "";
    var sqls="select question,link,ansuser,userlink,pic,anstext from "+TEST_QUESTION+" where topic='"+topic+"' order by voteSum DESC limit 5";

    client.query(sqls, function (err, res) {
        var length=5;
        if(res.length<=5)
        length=res.length;

        for(var i=0;i<length;i++)
        {

            con += "<h2>" +
                "<a href=\"" + "http://www.zhihu.com" +  res[i].link + "\">"+(i+1)+": " + res[i].question + "</a> <br>" +
                    //"<img src=\""+res[i].pic+"\"> </img>  <br>"+
                "<a href=\"" + "http://www.zhihu.com" +  res[i].userlink + "\">" + res[i].ansuser + "</a> <br>" +
                "<span text-overflow=ellipsis>"+res[i].anstext+"<a href=\"" + "http://www.zhihu.com"+res[i].link+"\"> 查看全部 </a>"+"</span>"+
                "</h2><br>";
        }
        con+="[wpfp-link]";			//添加加入收藏链接
        console.log(con);


        wpclient.newPost({
            title: nowDate+topic+"精选",
            terms:{'category':[3]},

            content: con,
            status:"future"
        }, function( error, data ) {
            console.log( arguments );
        });
    });
}


var coq = "";
var sqlq="select question,link from quoraquestion  order by voteSum DESC limit 20";

function getcon3(){
    client.query(sqlq, function (err, res) {
        var length=20;
        if(res.length<=20)
            length=res.length;
        for(var i=0;i<length;i++)
        {
            coq += ("<h4><a href=\"" + "https://www.quora.com" +  res[i].link + "\">"+(i+1)+": " + res[i].question + "</a></h4><br>");
        }
        coq+="[wpfp-link]";			//添加加入收藏链接
        console.log(coq);

        wpclient.newPost({
            title: nowDate+"quora精选",
            terms:{'category':[2]},
            content: coq,
            status:"future"
        }, function( error, data ) {
            console.log( arguments );
        });
    });
}



/*
getcon();
for(var j=0;j<topiclist.length;j++)
    getcon2(topiclist[j]);
 getcon3();

*/

exports.run = function()
{
    getcon();
    for(var j=0;j<topiclist.length;j++)
        getcon2(topiclist[j]);
    getcon3();
};