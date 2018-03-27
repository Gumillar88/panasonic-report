@include('layouts.header', ['title' => 'Upload Product - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Upload Product Model</h1>

    <div>
        @if($type === 'review')
        
            <h5>Review Data</h5>
            <p>Please review this data before being submitted to database. Make sure everything is right.</p>
        
            <table class="display initTable">
                <thead>
                    <tr>
                        <th data-width="5%">No.</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $key => $row)
                        <tr id="row-product-{{ $row['product'] }}" class="row-product queue">
                            <td>{{ $key + 1 }}</td>
                            <td class="cell cell-product" data-product="{{ $row['product'] }}">{{ $row['product'] }}</td>
                            <td class="cell cell-category" data-category="{{ $row['category'] }}">{{ $row['category'] }}</td>
                            <td class="cell cell-status"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        
            <p class="text-center">
                <a id="uploadProduct" class="btn btn-green">Upload</a>
                <a id="uploadLoading" class="btn btn-green hide">Uploading (<span id="uploadCounter"></span>)</a>
                <a href="{{ env('APP_HOME_URL') }}/product/category" id="uploadFinish" class="btn btn-green hide">Finish</a>
            </p>
        
        @else
            

            <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/model/upload" enctype="multipart/form-data">
                {!! csrf_field() !!}


                @if(count($errors) > 0)
                    <div class="note error">{{ $errors->all()[0] }}</div>
                @endif
                
                <div class="form-group">
                    <label for="excel">File</label>
                    <input type="file" class="form-control" name="excel" id="excel">
                    <p class="help-block">File format must be in Excel (.xlsx)</p>
                </div>
                
                <p class="text-center">
                    <input type="submit" class="btn btn-green" value="Create" />
                    <a href="{{ env('APP_HOME_URL') }}/product/category" class="btn">Back</a>
                </p>

            </form>
        
        @endif

    </div>

</div>

@include('layouts.footer');
