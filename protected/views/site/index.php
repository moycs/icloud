<?php $this->pageTitle=Yii::app()->name; ?>
<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<p>Congratulations! You have successfully created your Yii application.</p>

<p>You may change the content of this page by modifying the following two files:</p>
<ul>
	<li>View file: <tt><?php echo __FILE__; ?></tt></li>
	<li>Layout file: <tt><?php echo $this->getLayoutFile('main'); ?></tt></li>
</ul>

<p>For more details on how to further develop this application, please read
the <a href="http://www.yiiframework.com/doc/">documentation</a>.
Feel free to ask in the <a href="http://www.yiiframework.com/forum/">forum</a>,
should you have any questions.</p>

<?
$request = array(
        'request'=>array(
                'APIKey'=>'65d5661536cdc75c1fdf5c6de7bXrxh6FVxd9cFoOyIAVHD',
                'method'=>'authenticate',
                'data'=>array(
                	'email'=>'charlesportwoodii@ethreal.net',
			'password'=>''
		)
        )
);
print_r($request);

echo json_encode($request); 
$json = '{"request":{"method":"authenticate","data":{"email":"charlesportwoodii@ethreal.net","password":"Iyanyariem19a"},"APIKey":"65d5661536cdc75c1fdf5c6de7bXrxh6FVxd9cFoOyIAVHD"}}';
?>
<script>
        $(document).ready(function() {
                $.ajax({
                        url: 'http://duolci.ethreal.net/api/',
                        type: 'POST',
                        data: <? echo $json; ?>,
                        success: function(data, textStatus, jqXHR)
                        {
                                console.log(data);
                        }
                }); 
        });
</script>
