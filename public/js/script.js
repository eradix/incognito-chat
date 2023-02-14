const dateDiff = {
    inDays : function(d1,d2){
        var t2 = d2.getTime();
        var t1 = d1.getTime();
        return Math.floor((t2-t1)/(24*3600*1000));
    }
};

var dString = "Feb, 7, 2023";
 
var d1 = new Date(dString);
var d2 = new Date();

console.log(dateDiff.inDays(d1,d2));