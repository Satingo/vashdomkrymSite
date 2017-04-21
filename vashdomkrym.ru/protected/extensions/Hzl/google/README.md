--------------------------------------
-- Scott Huang's first widget 
-- Learn from Yiiwheels  
-- Dec 31, 2013  ;  Xiamen, China
-- Zhiliang.Huang@gmail.com
--------------------------------------

Change Log:
Sep 16, 2014: Add loadVersion parameter.


Examples:
<div class="row"> 
    <div class="span6" >  
        <?php
//very useful google chart
        $this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'PieChart',
            'data' => array(
                array('Task', 'Hours per Day'),
                array('Work', 11),
                array('Eat', 2),
                array('Commute', 2),
                array('Watch TV', 2),
                array('Sleep', 7)
            ),
            'options' => array('title' => 'My Daily Activity')));
        
        $this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'LineChart',
            'data' => array(
                array('Task', 'Hours per Day'),
                array('Work', 11),
                array('Eat', 2),
                array('Commute', 2),
                array('Watch TV', 2),
                array('Sleep', 7)
            ),
            'options' => array('title' => 'My Daily Activity')));
        ?>

    </div>
</div>

<div class="row"> 
    <div class="span6" >  
<?php
$this->widget('ext.Hzl.google.HzlVisualizationChart', 
        array('visualization' => 'Gauge','packages'=>'gauge',
    'data' => array(
        array('Label', 'Value'),
        array('Memory', 80),
        array('CPU', 55),
        array('Network', 68),
    ),
    'options' => array(
        'width' => 400,
        'height' => 120,
        'redFrom' => 90,
        'redTo' => 100,
        'yellowFrom' => 75,
        'yellowTo' => 90,
        'minorTicks' => 5
    )
));
?>
    </div>
</div>


<div class="row"> 
    <div class="span6" >  
<?php
  $this->widget('ext.Hzl.google.HzlVisualizationChart', array('visualization' => 'Map',
            'packages'=>'map',//default is corechart
            'loadVersion'=>1,//default is 1.  As for Calendar, you need change to 1.1
            'data' => array(
       ['Country', 'Population'],
        ['China', 'China: 1,363,800,000'],
        ['India', 'India: 1,242,620,000'],
        ['US', 'US: 317,842,000'],
        ['Indonesia', 'Indonesia: 247,424,598'],
        ['Brazil', 'Brazil: 201,032,714'],
        ['Pakistan', 'Pakistan: 186,134,000'],
        ['Nigeria', 'Nigeria: 173,615,000'],
        ['Bangladesh', 'Bangladesh: 152,518,015'],
        ['Russia', 'Russia: 146,019,512'],
        ['Japan', 'Japan: 127,120,000']
            ),
            'options' => array('title' => 'My Daily Activity',
                'showTip'=>true,
                )));

?>
    </div>
</div>