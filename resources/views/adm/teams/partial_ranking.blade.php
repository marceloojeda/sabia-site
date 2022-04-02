@foreach($rankingData as $week)
<div class="col-6">
    <div class="card text-white">
        <div class="card-header bg-info">
            <h4 class="card-title">
                Ranking das Equipes
                <span class="float-right text-dark text-muted small text-right">
                    {{ $week['title'] }}
                    <br />
                    {{ $week['totalSales'] }}
                </span>
            </h4>
            
        </div>
        <div class="card-body">
            <input type="hidden" name="canvas-ranking" data-canvas="{{ json_encode($week['ranking']) }}" data-id="canvas{{ $week['id'] }}">
            <canvas id="canvas{{ $week['id'] }}" ></canvas>
        </div>
        <div class="card-footer text-center">
            
        </div>
    </div>
</div>
@endforeach
