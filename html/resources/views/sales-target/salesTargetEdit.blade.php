@include('layouts.header', ['title' => 'Edit '.ucfirst($type).' Sales Target - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit {{ ucfirst($type) }} Sales Target</h1>
    
    @if(Session::has('sales-target-created'))
        <div class="note success">Sales target has been created.</div>
    @endif
    
    @if(Session::has('sales-target-updated'))
        <div class="note success">Sales target has been updated.</div>
    @endif

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/sales-target/edit">
            
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />
            <input type="hidden" name="type" value="{{ $type }}" />
            
            @if(count($errors) > 0)
                <div class="note error">{{ $errors->all()[0] }}</div>
            @endif

            @if($type === 'promotor')
                <div class="form-group">
                    <label for="promotor_name">Name</label>
                    <input type="text" class="form-control" name="promotor_name" id="promotor_name" value="{{ $promotor->name }}" readonly>
                </div>
            @endif
            
            <div class="form-group">
                <label for="dealer_name">Dealer</label>
                <input type="text" class="form-control" name="dealer_name" id="dealer_name" value="{{ $dealer->name }}" readonly>
            </div>

            <div class="form-group">
                <label for="date">Bulan</label>
                <input type="text" class="form-control" name="date" id="date" value="{{ $date }}" readonly>
            </div>

            <div class="form-group">
                <label for="total">Jumlah</label>
                <input type="text" name="total" class="form-control" value="{{ $total }}">
            </div>
            
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/sales-target?date={{ $date }}&type={{ $type }}" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
