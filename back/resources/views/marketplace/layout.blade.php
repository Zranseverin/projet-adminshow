@extends(auth()->check() && auth()->user()->account_type === 'admin' ? 'marketplace.admin-layout' : 'marketplace.seller-layout')
