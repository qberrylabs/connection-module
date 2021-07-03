@extends('layouts.app_admin')

@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="clearfix"></div>

      <div class="row">

        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2>User Connections</h2>

              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
              <p class="text-muted font-13 m-b-30"></p>

              <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>

                  <tr>
                    <th>id</th>
                    <th>User</th>
                    <th>With User</th>
                    <th>Status</th>
                    <th>Data</th>

                  </tr>
                </thead>
                <tbody>
                    @forelse ($connections as $connection)
                    <tr>
                        <td>#{{$connection->id}}</td>
                        <td>{{$connection->getUserInformaionByFromConnection->first_name}} {{$connection->getUserInformaionByFromConnection->last_name}}</td>
                        <td>{{$connection->getUserInformaionByWithConnection->first_name}} {{$connection->getUserInformaionByWithConnection->last_name}}</td>
                        <td><label class="badge badge-success">{{$connection->getConnectionStatus->connection_status_name}}</label></td>
                        <td>{{$connection->connection_date}}</td>
                      </tr>
                    @empty

                    @endforelse




                </tbody>
              </table>


            </div>
          </div>
        </div>
      </div>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection

