<h2><?php echo tFile::getT('module_install', 'License_agreement_s');?></h2>
<?php
if(isFree()){
	echo tFile::getT('module_install', 'freeLicenseText');
} 
else {
	echo tFile::getT('module_install', 'licenseText');
}