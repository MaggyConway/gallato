ChangeMailStatus=function(ID,SID){
	BX.style(BX('WAIT'+ID),'display','table-row');
	var params={
			AJAX:'Y',
			ACTION: 'CHANGESTATUSMAIL',
			ID: ID,
			ACTIVE: SID
	};
	BX.ajax({
		url: "/bitrix/tools/vbcherepanov.bonus/ajax.php",
		data: params,
		method: 'POST',
		dataType: 'html',
		timeout: 30,
		async: true,
		processData: true,
		scriptsRunFirst: true,
		emulateOnload: true,
		start: true,
		cache: false,
		onsuccess: function(data){
			BX('MT'+ID).innerHTML=data;
		},
		onfailure: function(){

		}
	});
	BX.style(BX('WAIT'+ID),'display','none');
};
ChangeAgentStatus=function(sid,ID,Active){
	BX.style(BX('WAITAG'+ID),'display','table-row');
	var interval=BX('AGENT_INTERVAL'+ID).value;
	var active=Active;
	var next=BX('NEXT_EXEC'+ID).value;
	var params={
		AJAX:'Y',
		ACTION: 'CHANGESTATUSAGENT',
		ID: ID,
		SITE:sid,
		ACTIVE:active,
		INTERVAL:interval,
		NEXT:next
	};
	BX.ajax({
		url: "/bitrix/tools/vbcherepanov.bonus/ajax.php",
		data: params,
		method: 'POST',
		dataType: 'html',
		timeout: 30,
		async: true,
		processData: true,
		scriptsRunFirst: true,
		emulateOnload: true,
		start: true,
		cache: false,
		onsuccess: function(data){
			BX(sid+'AG'+ID).innerHTML=data;
		},
		onfailure: function(){

		}
	});
	BX.style(BX('WAITAG'+ID),'display','none');
};
ChangeEventsStatus=function(sid,ID,Active){
	BX.style(BX('WAITEV'+ID),'display','table-row');
	var params={
		AJAX:'Y',
		ACTION: 'CHANGESTATUSEVENT',
		ID: ID,
		SITE:sid,
		ACTIVE:Active,
	};
	BX.ajax({
		url: "/bitrix/tools/vbcherepanov.bonus/ajax.php",
		data: params,
		method: 'POST',
		dataType: 'html',
		timeout: 30,
		async: true,
		processData: true,
		scriptsRunFirst: true,
		emulateOnload: true,
		start: true,
		cache: false,
		onsuccess: function(data){
			BX(sid+'EV'+ID).innerHTML=data;
		},
		onfailure: function(){

		}
	});
	BX.style(BX('WAITEV'+ID),'display','none');
};