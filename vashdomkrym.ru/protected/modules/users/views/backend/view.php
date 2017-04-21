<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment') => array('admin'),
	$model->email.($model->username != '' ? ' ('.$model->username.')' : ''),
);

$this->menu=array(
	array('label'=>Yii::t('common', 'User managment'), 'url'=>array('admin')),
	array('label'=>tt('Edit user'), 'url'=>array('update', 'id'=>$model->id), 'visible' => Yii::app()->user->checkAccess("admin")),
	array('label'=>tt('Delete user'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),
		'confirm'=>tc('Are you sure you want to delete this item?')), 'visible' => $model->role != User::ROLE_ADMIN),
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);
$model->scenario = 'backend';

$this->adminTitle = $model->email.($model->username != '' ? ' ('.$model->username.')' : '');
?>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data' => $model,
	'attributes'=>array(
		'id',
		array (
			'label' => CHtml::encode($model->getAttributeLabel('type')),
			'value' => $model->getTypeName(),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		array (
			'label' => CHtml::encode($model->getAttributeLabel('role')),
			'value' => $model->getRoleName(),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		'username',
		'email',
		'phone',
		array (
			'label' => CHtml::encode($model->getAttributeLabel('balance')),
			'value' => $model->balance,
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n",
			'visible' => (issetModule('paidservices')) ? true : false,
		),
		array (
			'label' => CHtml::encode($model->getAttributeLabel('additional_info')),
			'value' => $model->getAdditionalInfo(),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		array (
			'label' => tt('Status'),
			'value' => ($model->active) ? tt('Active') : tt('Inactive'),
			'template' => "<tr class=\"{class}\"><th>{label}</th><td>{value}</td></tr>\n"
		),
		'date_created',
		'last_login_date',
		'last_ip_addr',
	),
)); ?>

<?php
if (issetModule('tariffPlans') && issetModule('paidservices')) {
	$this->widget('application.modules.tariffPlans.components.userTariffInfoWidget', array('userId' => $model->id, 'showChangeTariffLnk' => false ));
}
?>
