<div class="bg-light shadow-sm rounded py-4 mb-4">
    <h3 class="text-5 font-weight-400 d-flex align-items-center px-4 mb-3">All Connections</h3>
    <!-- Title -->
    <div class="transaction-title py-2 px-4">
        <div class="row">
            <div class="col-2 col-sm-1 text-center"><span class="">Date</span></div>
            <div class="col col-sm-5">User</div>
            <div class="col-auto col-sm-1 d-none d-sm-block text-center">Status</div>
            
        </div>
    </div>
    <!-- Title End -->

    <!-- Transaction List  -->
    <div class="transaction-list">
        @foreach ($connections as $connection)
        <div class="transaction-item px-4 py-3" data-toggle="modal" data-target="#transaction-detail">
            <div class="row align-items-center flex-row">

                <div class="col-2 col-sm-1 text-center">
                    <span class="d-block text-1 font-weight-300 text-uppercase">{{$connection->connection_date}}</span>
                </div>

                <div class="col col-sm-5">
                    <span class="d-block text-4">{{$connection->user_name}}</span>
                    <span class="text-muted">{{ucfirst($connection->connection_action)}}</span>
                </div>

                <div class="col-auto col-sm-1 d-none d-sm-block text-center text-3">
                    @switch($connection->connection_status_id)
                        @case(1)
                            <span class="text-warning" data-toggle="tooltip" data-original-title="In Progress"><i class="fas fa-ellipsis-h"></i></span>
                            @break
                        @case(2)
                        <span class="text-success" data-toggle="tooltip" data-original-title="Completed"><i class="fas fa-check-circle"></i></span>
                            @break
                        @case(3)
                        <span class="text-danger" data-toggle="tooltip" data-original-title="Cancelled"><i class="fas fa-times-circle"></i></span>
                            @break
                        @default

                    @endswitch
                </div>

                <div class="col-3 col-sm-5 text-right text-4">

                    {{-- @if ($connection->connection_action == "Received")
                    <a class="btn btn-info d-inline-block" href="{{ route('connectionChangeStatus', ['connectionID'=>$connection->id,'status'=>'accept']) }}">Accept</a>
                    <a class="btn btn-danger d-inline-block" href="{{ route('connectionChangeStatus', ['connectionID'=>$connection->id,'status'=>'decline']) }}">Decline</a>
                    @else
                    <a class="btn btn-outline-danger d-inline-block" href="{{ route('connectionChangeStatus', ['connectionID'=>$connection->id,'status'=>'cancel']) }}">Cancel</a>
                    @endif --}}

                    <span class="text-nowrap">{{ucfirst($connection->getConnectionStatus->connection_status_name)}}</span>
                </div>
            </div>
        </div>
        @endforeach




    </div>
    <!-- Transaction List End -->

    <!-- View all Link -->
    {{-- <div class="text-center mt-4"><a href="transactions.html" class="btn-link text-3">View all<i class="fas fa-chevron-right text-2 ml-2"></i></a></div> --}}
    <!-- View all Link End -->

</div>
