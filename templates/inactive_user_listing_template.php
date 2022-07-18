<div class="wrap">
	<h1><?php echo ETSAEMAIL_PAGE_TITLE; ?></h1>
	<form method="post" action="options.php">
		<?php if ( isset( $_GET['settings-updated'] ) ) {			 
			echo "<div class='updated'><p>Facebook configuration details updated successfully.</p></div>";
		} ?>
		<?php settings_fields( 'etvuds-settings-group' ); 
			  //$myplug_options = get_option('wp2sfba_skip_fb_auth');	
		?>
		<?php do_settings_sections( 'etvuds-settings-group' ); ?>
		 
		<table class="form-table" border="0">
			<tr><td colspan="7" align="right"><?php echo $page_pagination_nav; ?></td></tr>
			<tr style="background-color:#1D2F83;">
				<th style="color:#FFFFFF;text-align:center">ID</th>
				<th style="color:#FFFFFF">Username</th>
				<th style="color:#FFFFFF">Driving Licence</th>
				<th style="color:#FFFFFF">Passport</th>
				<th style="color:#FFFFFF">ID Card</th>
				<th style="color:#FFFFFF">Bill</th>
				<th style="color:#FFFFFF">Status</th>
			</tr>
			<?php
				$etvud_counter = 1;
					foreach($arrUserMeta as $usrObj) { if(array_key_exists('usr_vdoc_isapproved',$usrObj)) { 
					$tblStyle = '';
					if($etvud_counter%2==0){
						$tblStyle = 'style="background-color: #ffffff"'; // 	
					}
					
					if($usrObj['usr_vdoc_isapproved']==1){
						$docIsApprovedLink = "Approved";
					} else {
						$docIsApprovedLink = "Not Approved";
					}
					/*
			?>			 
			<tr <?php echo $tblStyle;?>>
				<td style="text-align:center"><?php echo $usrObj['id']; ?> </td>
				<td><?php echo ucfirst($usrObj['first_name']).' '.ucfirst($usrObj['last_name']); ?> </td>
				<td><a style="cursor:pointer" onclick="window.open('<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_drivinglicense']); ?>', 'usr_vdoc_img_drivinglicense', 'width=400, height=350'); return false;"><img src="<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_drivinglicense']); ?>" width="50"></a></td>
				<td style="cursor:pointer"><a  onclick="window.open('<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_passport']); ?>', 'usr_vdoc_img_passport', 'width=400, height=350'); return false;"><img src="<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_passport']); ?>" width="50"></a></td>
				<td style="cursor:pointer"><a  onclick="window.open('<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_idcard']); ?>', 'usr_vdoc_img_idcard', 'width=400, height=350'); return false;"><img src="<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_idcard']); ?>" width="50"></a></td>
				<td style="cursor:pointer"><a  onclick="window.open('<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_bill']); ?>', 'usr_vdoc_img_bill', 'width=400, height=350'); return false;"><img src="<?php echo $this->eluTransGetImgPath($usrObj['usr_vdoc_img_bill']); ?>" width="50"></a></td>
				<td><a href="<?php echo 'admin.php?page='.$_GET['page'].'&dusrid='.$usrObj['id'].'&dsts='.$usrObj['usr_vdoc_isapproved'];?>"><?php echo $docIsApprovedLink; ?></a></td>
			</tr> 
			<?php */ $etvud_counter++; } } ?> 
			<tr><td colspan="7" align="right"><?php echo $page_pagination_nav; ?></td></tr>
		</table>		
		
	</form>
	</div>