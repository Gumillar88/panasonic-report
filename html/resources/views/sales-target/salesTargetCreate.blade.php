@include('layouts.header', ['title' => 'Set Sales Target for Promotor- Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Set Sales Target for Promotor</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/sales-target/create">
            
            {!! csrf_field() !!}
            
            <input type="hidden" name="promotor_ID" value="{{ $promotor->ID }}" />
            
            @if(count($errors) > 0)
                <div class="note error">{{ $errors->all()[0] }}</div>
            @endif
            
            <div class="form-group">
                <label for="promotor_name">Name</label>
                <input type="text" class="form-control" name="promotor_name" id="promotor_name" value="{{ $promotor->name }}" readonly>
            </div>
            
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
                <input type="text" name="total" class="form-control">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/sales-target?date={{ $date }}&type=promotor" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
