@extends('layouts.app')

@section('title', 'Menu Permissions - MES')
@section('page-title', 'Menu Permissions')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1"><i class="bi bi-shield-lock me-2"></i>{{ $designation->name }}</h5>
            <small class="text-muted">Select the active menus this designation may access.</small>
        </div>
        <a href="{{ route('designations.show', $designation) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <form method="POST" action="{{ route('designations.menu-permissions.update', $designation) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            @forelse($menus as $section => $sectionMenus)
                <section class="mb-4">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">{{ $section }}</h6>
                    <div class="row g-2">
                        @foreach($sectionMenus as $menu)
                            <div class="col-md-6 col-xl-4">
                                <label class="border rounded p-3 d-flex align-items-center gap-2 w-100 h-100">
                                    <input class="form-check-input mt-0" type="checkbox" name="menu_ids[]" value="{{ $menu->id }}"
                                        @checked(in_array($menu->id, $grantedMenuIds, true))>
                                    <span>
                                        <span class="d-block fw-semibold">{{ $menu->name }}</span>
                                        <small class="text-muted">{{ $menu->route_name }}</small>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="text-center text-muted py-4">No active menus have been registered.</div>
            @endforelse
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Menu Permissions</button>
        </div>
    </form>
</div>
@endsection
