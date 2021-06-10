
<div id="create-connection" class="modal fade " role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title font-weight-400">Create Connection</h5>
        <button type="button" class="close font-weight-400" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
    </div>
    <div class="modal-body p-4">
        <form id="create-connection-form" method="post" action="{{ route('connection.store') }}">
            @csrf
            <div class="row">
                <div class="col-12">
                <div class="form-group">
                    <label for="mobileNumber">User Name <span class="text-muted font-weight-500">(Required)</span></label>
                    {{-- <input type="text" name="with_user_id" class="form-control" data-bv-field="with_user_id" id="create-connection-name" required placeholder="User Name"> --}}

                    {{-- @php
                        $userConnections = App\Models\User::all();
                    @endphp --}}
                    <select name="with_user_id" value="{{ old('with_user_id') }}" data-style="custom-select bg-transparent border-0"  data-container="body" data-live-search="true" class="selectpicker form-control @error('with_user_id') is-invalid @enderror" required="">
                        @foreach ($userConnections as $userConnection)
                            @isset($userConnection->user)
                                <option value="{{$userConnection->user->id}}">{{$userConnection->user->name}}</option>
                            @endisset

                        @endforeach
                    </select>

                    @error('with_user_id')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                </div>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Save Changes</button>
        </form>
    </div>
    </div>
</div>
</div>
