
<?php if (!empty($_POST['save_options'])): ?>
<div class="updated"><p><strong>Modifications enregistr&eacute;es.</strong></p></div>
<?php endif; ?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>RDPano - Panoramas krpano et panotour&reg;</h2>
	<form action="<?php echo $formAction; ?>" method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Taille du panorama</th>
					<td>
						<label for="rdpano_width">Largeur&nbsp;:</label>
						<input type="text" id="rdpano_width" name="rdpano_width" size="6" value="<?php echo $this->options['rdpano_width']; ?>" /> <acronym title="pixels">px</acronym>
						<label style="margin-left: 40px;" for="rdpano_height">Hauteur&nbsp;:</label>
						<input type="text" id="rdpano_height" name="rdpano_height" size="6" value="<?php echo $this->options['rdpano_height']; ?>" /> <acronym title="pixels">px</acronym>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="rdpano_title">Info-bulle de la preview</label></th>
					<td>
						<input type="text" id="rdpano_title" name="rdpano_title" size="60" value="<?php echo $this->options['rdpano_title']; ?>" />
						<p><small><em>Ce texte est affich&eacute; dans une info-bulle lorsqu'on survole l'image de pr&eacute;visualisation (=preview) du panorama.</em></small></p>
					</td>
				</tr>
				<?php /* ?>
				<tr valign="top">
					<th scope="row"><label for="rdpano_global_swf">Lecteur SWF commun</label></th>
					<td>
						<input type="checkbox" id="rdpano_global_swf_chk" name="rdpano_global_swf_chk" onclick="var d = document.getElementById('rdpano_global_swf_div'); if (this.checked){ d.style.display = ''; document.getElementById('rdpano_global_swf').focus(); } else { d.style.display = 'none'; }" value="1"<?php if ($this->options['rdpano_global_swf'] != ''){ echo ' checked="checked"'; } ?> />
						<label for="rdpano_global_swf_chk">Utiliser un lecteur SWF commun.</label>
						<p><small><em>Utiliser qu'un seul fichier SWF plut&ocirc;t que un nouveau &agrave; chaque panorama optimise le surf de votre visiteur car un seul
							fichier&nbsp;.swf ne sera t&eacute;l&eacute;charg&eacute;&nbsp;=&nbsp;optimisation notamment sur les mobiles&hellip;</em></small></p>
						
						<div id="rdpano_global_swf_div"<?php if ($this->options['rdpano_global_swf'] == ''){ echo ' style="display: none;"'; } ?>>
							<label for="rdpano_global_swf">Emplacement du SWF commun&nbsp;:</label>
							<input type="text" id="rdpano_global_swf" name="rdpano_global_swf" size="60" value="<?php echo $this->options['rdpano_global_swf']; ?>" />
							<span class="description">Exemple&nbsp;: wp-content/uploads/-global/build.swf</span>
						</div>
					</td>
				</tr>
				<?php */ ?>
				<tr valign="top">
					<th scope="row"><label for="rdpano_panopress">Compatibilit&eacute; PanoPress</label></th>
					<td>
						<input type="checkbox" id="rdpano_panopress" name="rdpano_panopress" value="1"<?php if ($this->options['rdpano_panopress'] == '1'){ echo ' checked="checked"'; } ?> />
						<label for="rdpano_panopress">Utiliser &eacute;galement la syntaxe [pano file=&quot;&quot;] utilis&eacute;e par le module PanoPress</label>
						<p><small><em>Cocher la case si vous utilisiez le plugin PanoPress et que vous ne souhaitez plus l'utiliser (pensez &agrave; le d&eacute;sactiver).</em></small></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="save_options" value="Enregistrer" class="button-primary" /></p>
	</form>
</div>