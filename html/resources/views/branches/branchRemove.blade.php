@include('layouts.header', ['title' => 'Remove Branch - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Branch</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/branch/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $branch->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the branch "{{ $branch->name }}"?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/branch" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
