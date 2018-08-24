<?php
/**
 * @author John Snook
 * @date Aug 23, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of visits_map
 */
echo "Picture a big old graph here.";
?>
<?php foreach ($calendar as $year => $yearCalendar): ?>
    <h3><?= Html::a($year, ['marriage/year', 'year' => $year]) ?></h3>
    <div class="chart" data-id="<?= $year ?>"></div>
    <script>
        theWork.push(function () {
            var chartData<?= $year ?> = <?= json_encode($yearCalendar) ?>;
            for (i = 0; i < chartData<?= $year ?>.length; i++) {
                chartData<?= $year ?>[i].date = new Date(chartData<?= $year ?>[i].date + ' 12:00');
            }
            //debug;
            var chart<?= $year ?> = calendarHeatmap()
                    .data(chartData<?= $year ?>)
                    .startDate(new Date('<?= $year ?>-01-01'))
                    .tooltipUnit('Messages')
                    .selector('.chart[data-id="<?= $year ?>"]')
                    .colorRange(['#E7F8E6', '#4B6415'])
                    .tooltipEnabled(true)
                    .onClick(function (data) {
                        let datey = data.date.toISOString().slice(0, 10);
                        location.href = '<?= yii\helpers\Url::toRoute(['year', 'year' => $year]) ?>&date=' + datey;
                        //console.log('onClick callback. Data:', data);
                    });
            chart<?= $year ?>();  // render the chart
        });
    </script>
<?php endforeach; ?>