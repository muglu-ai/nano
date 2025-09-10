@extends('layouts.dashboard')
@section('title', 'Co - Exhibitors List')
@section('content')



<style>
    .badge {
        font-size: 0.2rem;
        padding: 0.25rem 0.50rem;
        border-radius: 0.30rem;
    }

    th {
        text-align: left !important;
        padding-left: 8px !important;
    }

    #products-list {
        table-layout: fixed;
        width: 100%;
    }

    #products-list th:nth-child(1) {
        width: 20%;
    }

    /* Co-Exhibitor Name */
    #products-list th:nth-child(2) {
        width: 25%;
    }

    /* Co-Exhibitor Under */
    #products-list th:nth-child(3) {
        width: 15%;
    }

    /* Contact Person */
    #products-list th:nth-child(4) {
        width: 20%;
    }

    /* Email */
    #products-list th:nth-child(5) {
        width: 10%;
    }

    /* Status */
    #products-list th:nth-child(6) {
        width: 10%;
    }


    /* Action */
    #products-list th:nth-child(7) {
        width: 10%;
    }

    /* Handle text overflow */
    #products-list td,
    #products-list th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 8px;
    }

    /* Add hover effect to show full text */
    #products-list td:hover {
        white-space: normal;
        overflow: visible;
        position: relative;
    }


    /*  #products-list td,
                #products-list th {
                    border-left: none;
                    border-right: none;
                }*/
</style>
<div class="container-fluid mt-0 py-2">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="mb-4">Co-Exhibitors List</h2>
                    <div class="table-responsive">
                        <table class="table table-flush  coe" id="products-list">
                            <thead class="thead-light table-dark">
                                <tr>
                                    <th class="text-uppercase">Co-Exhibitor Name</th>
                                    <th class=" text-uppercase">Co-Exhibitor Under</th>

                                    <th class=" text-uppercase">Contact Person</th>
                                    <th class=" text-uppercase">Email & Contact Number</th>
                                    <th class=" text-uppercase">Proof Of Document</th>
                                    <th class=" text-uppercase text-center">Status</th>
                                    <th class=" text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coExhibitors as $coExhibitor)
                                <tr>
                                    <td class="text-md fw-bold">{{ $coExhibitor->co_exhibitor_name }}</td>
                                    <td>
                                        <a class="text-md text-info "
                                            href="{{ route('application.view', ['application_id' => $coExhibitor->application->application_id]) }}">{{ $coExhibitor->application->company_name }}
                                        </a>
                                    </td>

                                    <td class="text-md  fw-bold">{{ $coExhibitor->contact_person }}</td>
                                    <td class="text-md fw-bold">{{ $coExhibitor->email }} <br>
                                        {{ $coExhibitor->phone }}
                                    </td>
                                    <td>
                                        @if ($coExhibitor->proof_document)
                                        <a href="{{ asset('' . $coExhibitor->proof_document) }}"
                                            class="btn btn-secondary btn-sm" target="_blank">View <br>Document</a>
                                        @else
                                        <span class="text-muted">No Document</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge w-100 text-md fw-bold 
        @if($coExhibitor->status == 'pending') badge-warning
        @elseif($coExhibitor->status == 'rejected') badge-danger
        @else badge-success
        @endif">
                                            {{ ucfirst($coExhibitor->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($coExhibitor->status == 'pending')
                                        <button onclick="approveCoExhibitor({{ $coExhibitor->id }})"
                                            class="btn btn-success btn-sm ">Approve</button>
                                        <br>
                                        <button onclick="rejectCoExhibitor({{ $coExhibitor->id }})"
                                            class="btn btn-danger btn-sm">Reject</button>
                                        @else
                                        <span class="text-muted text-md text-dark">No actions available</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function approveCoExhibitor(id) {
            Swal.fire({
                title: "Approve Co-Exhibitor?",
                // text: "Approve Co-Exhibitor.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Approve",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/co-exhibitor/approve/${id}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json",
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire("Success!", data.message, "success").then(() => location.reload());
                        });
                }
            });
        }

        function rejectCoExhibitor(id) {
            Swal.fire({
                title: "Reject Co-Exhibitor?",
                text: "This cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Reject",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/co-exhibitor/reject/${id}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json",
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire("Done!", data.message, "success").then(() => location.reload());
                        });
                }
            });
        }
    </script>





    <script>
        function changeFontSize(size) {
            let cells = document.querySelectorAll("#products-list td"); // Selects all td inside the table with id 'coe'
            cells.forEach(cell => {
                cell.style.fontSize = size + "5px";
            });
        }
    </script>
    @endsection