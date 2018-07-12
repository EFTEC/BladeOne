@set($x1=20)
Injection:<hr>
@inject("metric" , 'App\Services\MetricsService')
@inject("simpleclass" ,'')
<hr>
<div>
    Monthly Revenue: {{ $metric->monthlyRevenue() }}.
</div>
<div>
    Ping: {{ $simpleclass->ping('pong!') }}.
</div>


@inject("simpleclass" )

<div>
    Ping Again: {{ $simpleclass->ping('pong again!') }}.
</div>