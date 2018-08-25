@set($x1=20)
Injection:<hr>
@inject("metric" , 'MetricsService\Metric')
@inject("simpleclass" ,'SimpleClass')
<hr>
<div>
    Monthly Revenue: {{ $metric->monthlyRevenue() }}.
</div>
<div>
    Ping: {{ $simpleclass->ping('pong!') }}.
</div>


@inject("ohterSimpleClass", "SimpleClass")

<div>
    Ping Again: {{ $ohterSimpleClass->ping('pong again!') }}.
</div>