@include('layouts.header', ['title' => 'Product Type - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Product Type</h1>
    

    @if(Session::has('type-created'))
        <div class="note success">Product Category has been created.</div>
    @endif

    @if(Session::has('type-deleted'))
        <div class="note error">Product type has been removed.</div>
    @endif

    <div>
        <h5>Category: {{$category_name}}</h5></br>
        <a href="{{ env('APP_HOME_URL') }}/product/category" class="btn">Back</a>
        <a href="{{ env('APP_HOME_URL') }}/product/type/create" class="btn btn-green">Create</a>
    </div>

    <table id="categoryTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="30%">Type Name</th>
                <th data-width="20%">Category</th>
                <th data-width="35%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $key => $type)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $type->name }}</td>
                    <td>{{ $categories[$type->category_ID] }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/product/model/?type_ID={{ $type->ID }}" class="btn btn-green">View</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/type/edit?ID={{ $type->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/type/remove?ID={{ $type->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
