// JavaScript Document
jQuery().ready(function(){	

	jQuery('.tzcLocal').bind('click', tzcLocal); 
	jQuery('.tzcClient').bind('click', tzcClient); 
	tzcInit('dd/MM HH:mm');

	jQuery('#menu_groups li a').click(
		function(e) {
			e.preventDefault();
			var checkElement = jQuery(this);
			jQuery('#menu_groups li a').each(function(i) {
			    jQuery(this).removeClass('sel');
			});
			checkElement.addClass('sel');
			var ahref = checkElement.attr('href');
			var parm = ahref.split(',',3);
			var url = parm[2]+"/wp-content/plugins/wp-football/football-functions.php";
			jQuery.ajax({
				url: url,
				dataType: 'html',
				type: 'GET',
				data: { id_league: parm[0], id_group: parm[1], ajax: 1, f: 'get_groups' },
				beforeSend: function() {
					jQuery('.wpf_loading').show();
				},
				complete: function() {
					jQuery('.wpf_loading').hide();
				},
				success: function(data, textStatus) {
					jQuery('.dtabTeams').empty();
					jQuery('.dtabTeams').html(data);
				},
				error: function(xhr,er) {
					alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
				}
			});	
			jQuery.ajax({
				url: url,
				dataType: 'html',
				type: 'GET',
				data: { id_league: parm[0], id_group: parm[1], ajax: 1, f: 'get_matches' },
				beforeSend: function() {
					jQuery('.wpf_loading').show();
				},
				complete: function() {
					jQuery('.wpf_loading').hide();
				},
				success: function(data, textStatus) {
					jQuery('.dtabMatches').empty();
					jQuery('.dtabMatches').html(data);
				},
				error: function(xhr,er) {
					alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
				}
			});	
        }
	);

	jQuery('.menu_phases li a').click(
		function(e) {
			e.preventDefault();
			var checkElement = jQuery(this);
			jQuery('.menu_phases li a').each(function(i) {
			    jQuery(this).removeClass('sel');
			});
			checkElement.addClass('sel');
			var ahref = checkElement.attr('href');
			var parm = ahref.split(',',3);
			var url = parm[2]+"/wp-content/plugins/wp-football/football-functions.php";
			jQuery.ajax({
				url: url,
				dataType: 'html',
				type: 'GET',
				data: { id_league: parm[0], id_phase: parm[1], ajax: 1, f: 'get_matches' },
				beforeSend: function() {
					jQuery('.wpf_loading').show();
				},
				complete: function() {
					jQuery('.wpf_loading').hide();
				},
				success: function(data, textStatus) {
					jQuery('.dtabMatches').empty();
					jQuery('.dtabMatches').html(data);
				},
				error: function(xhr,er) {
					alert('Error '+xhr.status+' - '+xhr.statusText+', Type Error: '+er+' >> url: '+url);
				}
			});	
        }
	);

});	

var tzcLocal = function() {
	jQuery(".tzcLocal").hide();
	jQuery(".tzcClient").show();
	var mts=jQuery(".datetime");
	jQuery.each(mts, function(i,mt) {
		if(mt['title']!=null&&mt['title'].length>0) {
			var s=mt['title'].split(',')
			if(s.length>0) {
				var t=tzc.formatDate(tzc.decodeUTC(s[1]));
				jQuery(mt).text(t);
				jQuery(mt).addClass('TZCclient');
			}
		}
	});
	Cookie.set('wpfootball','0',365,'/');
}

var tzcClient = function() {
	jQuery(".tzcClient").hide();
	jQuery(".tzcLocal").show();
	var mts=jQuery(".datetime");
	jQuery.each(mts, function(i,mt) {
		if(mt['title']!=null&&mt['title'].length>0) {
			var s=mt['title'].split(',')
			if(s.length>0) {
				jQuery(mt).text(s[0]);
				jQuery(mt).removeClass('TZCclient');
			}
		}
	});
	Cookie.set('wpfootball','1',365,'/');
}

var tzcInit = function(sFormat) {
	if(sFormat!==undefined&&sFormat!=null&&sFormat!='') tzc.tFmt=sFormat;
	var c=Cookie.get('wpfootball');
	if(c!==undefined&&c!=null&&c=='1')
		tzcClient();
	else
		tzcLocal();
}

var tzc={
	tFmt:'dd/MM/yy HH:mm',
	padString:function(s,l,pc) { while (s.length<l) { s=pc + s };return s;},
	formatDate:function(d) {
		var _d=tzc.padString(d.getDate().toString(),2,'0'),_M=tzc.padString((d.getMonth() + 1).toString(),2,'0'),_y=tzc.padString((d.getFullYear() % 1000).toString(),2,'0'),_h=tzc.padString(d.getHours().toString(),2,'0'),_m=tzc.padString(d.getMinutes().toString(),2,'0');
		if(tzc.tFmt=='dd/MM/yy HH:mm') return _d + '/' + _M + '/' + _y + ' ' + _h + ':' + _m;
		if(tzc.tFmt=='dd/MM HH:mm') return _d + '/' + _M + ' ' + _h + ':' + _m;
		if(tzc.tFmt=='HH:mm') return _h + ':' + _m;
		if(tzc.tFmt=='dd/MM/yy') return _d + '/' + _M + '/' + _y;
		return _d + '/' + _M + '/' + _y + ' ' + _h + ':' + _m;
	},
	decodeUTC:function(n) {
		var _m=n % 100,_h=Math.floor(n / 100) % 100,_d=Math.floor(n / 10000) % 100,_M=(Math.floor(n / 1000000) % 100) - 1,_y=Math.floor(n / 100000000),_dt=new Date();
		_dt.setTime(Date.UTC(_y,_M,_d,_h,_m));return _dt
	}
}

var Cookie={
setRaw:function(n,v,daysToExp,pg){
var ex='';
try{
if(daysToExp!=undefined){
var d=new Date();
d.setTime(d.getTime()+(86400000*parseFloat(daysToExp)));
ex=';expires='+d.toGMTString();}
}catch(e){}
if(pg!=undefined){if(pg!='.')ex+=';path='+pg;}
else {ex+=';path=/';}
return(document.cookie=escape(n)+'='+(v||'')+ex);
},
set:function(n,v,daysToExp,pg){
return this.setRaw(n,escape(v||''),daysToExp,pg);
},
get:function(n){
var c=document.cookie.match(new RegExp('(^|;)\\s*'+escape(n)+'=([^;\\s]*)'));
return(c?unescape(c[2]):null);
},
erase:function(n,pg){
var c=Cookie.get(n)||true;
Cookie.set(n,'',-1,pg);
return c;
},
accept:function(){
if(typeof(navigator.cookieEnabled)=='boolean'){return navigator.cookieEnabled;}
Cookie.set('_t','1');return(Cookie.erase('_t')==='1');
}
};
