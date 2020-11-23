@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net/dataTables.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('customers.index')}}">Invoice</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lists</li>
  </ol>

</nav>




<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
   
    <div class="d-flex align-items-center flex-wrap text-nowrap">
    
   
    
    <a href="{{route('invoice.create')}}"><button type="button" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
        <i class="fa fa-plus"></i>
      Create New Invoice
      </button></a>
    </div>
  </div>


  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
         <div class="table-responsive">
            <table id="dataTableExample" class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Invoice ID</th>
                  <th>Customer Name</th>
                  <th>Invoice Date</th>
                  <th>Provider Name</th>
                  <th>Net Amount</th>
                  <th>View Invoice</th>
                  <th>Download Invoice</th>
                  <th>Invoice Lists</th>
                </tr>
              </thead>
              <tbody>

         @php $i=0; @endphp

         @foreach ($invoices as $invoice)
         <tr>

          <th>#</th>
            <td class="text-success"><b>MDN-{{$invoice->id}}</b></td>
            <td>{{$invoice->customer->fullname}}</td>
            <td class="text-primary">{{$invoice->invoice_date}}</td>
            <td class="text-secondary">@php echo ($invoice->provider == 1)? "TNT" :"FeDex" @endphp</td>
            <td class="text-danger"> <b> &#8377;  {{$invoice->net_amount}} </b></td>
            <td><button class="btn btn-md btn-warning"><i class="fa fa-eye"></i></button></td>
            <td><a href="{{route('invoice.download',$invoice->id)}}"><button class="btn btn-md btn-primary"><i class="fa fa-files-o"></i></button></a></td>
            <td><button class="btn btn-md btn-secondary"><i class="fa fa-list"></i></button></td>
                
              
          
          
          
          
          
          </tr>
         @endforeach

    
               
             
           
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>






@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs4/dataTables.bootstrap4.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
@endpush