module.exports =
{	
	adddays	: function(baseday,days,type){
		let strsplite = "/" ;
		if(type==1)
			strsplite = "-" ;
		let arrtmpday = baseday.split(strsplite) ;
		let myDate = new Date(parseInt(arrtmpday[0]),parseInt(arrtmpday[1])-1,parseInt(arrtmpday[2]));

	  	//myDate.setMonth(myDate.getMonth()+6); 
	  	myDate.setDate(myDate.getDate() + days);
	  	let MM = myDate.getMonth() + 1;
	  	let dd  = myDate.getDate();
		if(MM<10)
		{
			MM='0'+MM;
		}
		if(dd<10)
		{
			dd='0'+dd;
		}
	  	return myDate.getFullYear()+strsplite+MM+strsplite+dd ;
	},
	countday : function(sd,ed,type){
		let days = 0 ;
		while(sd != ed)
		{
			sd = this.adddays(sd,1,type) ;
			days++ ;
			if(days > 1000) break ;
		}
		return days ;
	},
	everyday : function(sd,ed,type){
		let data = [] ;
		let days = this.countday(sd,ed,type) ;
		for(var i=1 ; i <= days ; i++)
		{
			let nowsd = sd ;
			sd = this.adddays(sd,1,type) ;
			data.push({sd : nowsd , ed : sd }) ;
		}
		return data ;
	},
	everydaynoadd : function(sd,ed,type){
		let data = [] ;
		let days = this.countday(sd,ed,type) ;
		for(var i=0 ; i <= days ; i++)
		{
			let nowsd = sd ;
			sd = this.adddays(sd,1,type) ;
			data.push({sd : nowsd , ed : nowsd }) ;
		}
		return data ;
	},
	test : function(sd,ed){
		let data = this.everydaynoadd(sd,ed,1) ;
		for(let days of data){
			console.log("sd : "+days.sd+" ed : "+days.ed) ;
		}
		console.log(data) ;
		//console.log("開始時間: "+sd+" 結束時間: "+ed) ;
		//let days = this.countday(sd,ed) ;
		//console.log("時間差距 "+days+" 天") ;
	}
}