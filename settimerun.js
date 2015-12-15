/**
 * Created by Administrator on 2015/6/12.
 */
var schedule = require("node-schedule");


console.log("莫急");


var rule1 = new schedule.RecurrenceRule();
rule1.dayOfWeek = [0, new schedule.Range(1, 6)];
rule1.hour = 11;
rule1.minute = 42;

var j1 = schedule.scheduleJob(rule1, function(){
    console.log("开始执行抓取知乎用户任务");
    var grasppeople=require("./grasppeople.js");
    grasppeople.run();

});


var rule2 = new schedule.RecurrenceRule();
rule2.dayOfWeek = [0, new schedule.Range(1, 6)];
rule2.hour = 11;
rule2.minute = 57;

var j2 = schedule.scheduleJob(rule2, function(){
    j1.cancel();
    console.log("开始执行抓取知乎问题任务");
    var graspquestion=require("./graspquestion.js");
    graspquestion.run();

});

var rule3 = new schedule.RecurrenceRule();
rule3.dayOfWeek = [0, new schedule.Range(1, 6)];
rule3.hour = 12;
rule3.minute = 10;

var j3 = schedule.scheduleJob(rule3, function(){
    j2.cancel();
    console.log("开始执行抓取quora任务");
    var graspquora=require("./graspquora.js");
    graspquora.run();

});


var rule4 = new schedule.RecurrenceRule();
rule4.dayOfWeek = [0, new schedule.Range(1, 6)];
rule4.hour = 12;
rule4.minute = 20;

var j4 = schedule.scheduleJob(rule4, function(){
    j3.cancel();
    console.log("开始执行post问题到wordpress任务");
    var postdata=require("./postdata.js");
    postdata.run();

});