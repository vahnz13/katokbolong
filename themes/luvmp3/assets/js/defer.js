jQuery(function() {
	jQuery( "#vol-search" ).autocomplete({
		source: function( request, response ) {
			var sqValue = [];
				jQuery.ajax({
					type: "POST",
					url: "https://suggestqueries.google.com/complete/search?hl=en&ds=yt&client=youtube&hjson=t&cp=1",
					dataType: 'jsonp',
				data: jQuery.extend({
					q: request.term
				}, {  }),
				success: function(data){
					console.log(data[1]);
					obj = data[1];
				jQuery.each( obj, function( key, value ) {
					sqValue.push(value[0]);
				});
			response( sqValue);
			}
		});
		}
 	});
});

jQuery(function() {
	$("#hide").click(function(){
		$("#wait").show();
	});
$("#hide").click(function(){
	setTimeout(function(){
		$("#wait").hide();
		$("#link").toggle();
	}, 2000);
	});
});

var _0xf3da=["\x73\x68\x6F\x77","\x23\x70\x72\x6F\x67\x72\x65\x73\x73","\x61\x6A\x61\x78\x53\x74\x61\x72\x74","\x68\x69\x64\x65","\x75\x6E\x62\x69\x6E\x64","\x61\x6A\x61\x78\x53\x74\x6F\x70","\x2F\x63\x6F\x6E\x66\x69\x67\x2F\x6A\x61\x76\x61\x2E\x70\x68\x70","\x68\x74\x6D\x6C","\x23\x66\x69\x6E\x61\x6C","\x70\x6F\x73\x74","\x63\x6C\x69\x63\x6B","\x23\x64\x6C\x42\x75\x74\x74\x6F\x6E"];$(_0xf3da[11])[_0xf3da[10]](function(){$(document)[_0xf3da[2]](function(){$(_0xf3da[1])[_0xf3da[0]]()});$(document)[_0xf3da[5]](function(){$(_0xf3da[1])[_0xf3da[3]]();$(this)[_0xf3da[4]](_0xf3da[2])});$[_0xf3da[9]](_0xf3da[6],{id:id,name:name},function(_0x6596x1){$(_0xf3da[8])[_0xf3da[7]](_0x6596x1)})})