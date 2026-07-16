@extends('layouts.layout')

@section('title', 'Admin Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h4>Admin Dashboard</h4>
        <!-- <div class="breadcrumb-trail">
            <a href="{{ route('dashboard') }}">Home</a> &nbsp;/&nbsp; <span>Dashboard</span>
        </div> -->
    </div>
</div>

<style>

    footer.footer
    {
        display: none;
    }
</style>