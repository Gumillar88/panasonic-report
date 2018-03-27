@include('layouts.header', ['title' => 'Remove Region - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Region</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/region/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $region->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the region "{{ $region->name }}"?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/region" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
