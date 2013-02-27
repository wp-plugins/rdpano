(function() {
	tinymce.create('tinymce.plugins.RDPanoPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceRDPano', function() {
				ed.windowManager.open({
					file : url + '/rdpano.htm',
					width :450 + parseInt(ed.getLang('rdpano.delta_width', 0)),
					height : 300 + parseInt(ed.getLang('rdpano.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});
 
			// Register buttons
			ed.addButton('rdpano', {title : 'RDPano', cmd : 'mceRDPano', image: url + '/button.png' });
		},
 
		getInfo : function() {
			return {
				longname : 'RDPano',
				author : 'Roland Dufour',
				authorurl : 'http://www.rd-creation.fr/rdpano',
				infourl : 'http://www.rd-creation.fr/rdpano',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
 
	// Register plugin
	tinymce.PluginManager.add('rdpano', tinymce.plugins.RDPanoPlugin);
})();