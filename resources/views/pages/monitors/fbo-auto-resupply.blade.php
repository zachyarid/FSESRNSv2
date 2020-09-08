@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-box tilebox-two">
                <input type="hidden" id="param_id" value="{{ $param->id }}" />
                <div class="form-group row">
                    <label for="resupply_days" class="col-sm-2 form-control-label">Resupply at days remaining</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="resupply_days" value="{{ $param->resupply_days }}" />
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted">This is when (in days) that each FBO will be resupplied</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="resupply_amount" class="col-sm-2 form-control-label">Resupply amount</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="resupply_amount" value="{{ $param->resupply_amount }}"/>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted">Amount of supplies to purchase in days for each selected FBO</small>
                    </div>
                </div>

                <button class="btn btn-success" onclick="changeParams()">Save</button>
            </div>
        </div>
    </div>

    <form action="{{ route('monitor.saveauto', $subscription->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card-box tilebox-two">
                    <div class="form-group row">
                        <button class="btn btn-success m-b-1" id="select-all">Select All</button>
                        <button class="btn btn-danger m-b-1" id="deselect-all">De-Select All</button>
                        <select name="selected_fbos[]" class="multi-select" multiple id="selected_fbos">
                            @forelse($allfbos as $fbo)
                                <option value="{{ $fbo->id }}"> {{ $fbo->icao . ' (' . $fbo->supplied_days . ')'}} </option>
                            @empty
                                <option>This group does not seem to have any FBOs. Did you select the correct group?</option>
                            @endforelse
                        </select>
                        <small class="text-muted">The number in parentheses represents the number of days the FBO is supplied</small>
                    </div>

                    <div class="form-group row">
                        <button class="btn btn-success">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script-source')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#selected_fbos').multiSelect({
                selectableHeader: "<input type=\"text\" class=\"form-control search-input\" autocomplete=\"off\" placeholder=\"ICAO\">",
                selectionHeader: "<input type=\"text\" class=\"form-control search-input\" autocomplete=\"off\" placeholder=\"ICAO\">",
                afterInit: function(ms){
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function(e){
                            if (e.which === 40){
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function(e){
                            if (e.which == 40){
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                },
                afterSelect: function(){
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function(){
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });

            $('#select-all').click(function(){
                $('#selected_fbos').multiSelect('select_all');
                return false;
            });
            $('#deselect-all').click(function(){
                $('#selected_fbos').multiSelect('deselect_all');
                return false;
            });
        });

        function changeParams()
        {
            var days = $('#resupply_days').val();
            var amount = $('#resupply_amount').val();
            var id = $('#param_id').val();

            $.ajax({
                type: 'POST',
                url: '{{ route('monitor.changearp') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    days: days,
                    amount: amount,
                    subscription: {{ $param->subscription_id }}
                },
                success: function (data) {
                    if (data.success)
                    {
                        toastr["success"](data.message,
                            "Success!", {
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": false,
                                "progressBar": false,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "1000",
                                "hideDuration": "500",
                                "timeOut": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            });
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    </script>
@endsection