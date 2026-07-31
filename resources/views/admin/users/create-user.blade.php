@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้งานใหม่')

@section('content')
<div class="container mx-auto p-4 md:p-6 max-w-4xl animate-fade-in">

    <div class="bg-white shadow-xl rounded-3xl p-6 md:p-8 border border-gray-100 space-y-6">
        
        {{-- Header & Back Navigation --}}
        <div class="flex items-center justify-between pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                    เพิ่มผู้ใช้งานใหม่
                </h1>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mt-1">
                    กรอกข้อมูลเพื่อลงทะเบียนผู้ใช้งานใหม่เข้าสู่ระบบ
                </p>
            </div>
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                ย้อนกลับ
            </a>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)" 
                 class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3 text-sm font-bold">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ __(session('success')) }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 font-black">&times;</button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 7000)" 
                 class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm space-y-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>กรุณาตรวจสอบและแก้ไขข้อผิดพลาดต่อไปนี้:</span>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-black">&times;</button>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 font-medium pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ __($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add User Form --}}
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Full Name --}}
            <div>
                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                    ชื่อ-นามสกุล <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="เช่น สมชาย ใจดี"
                       class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                       required>
            </div>

            {{-- Email & Role Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        อีเมล <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="name@example.com"
                           class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                           required>
                </div>
                
                {{-- Role Select --}}
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        บทบาท <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" 
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                            required>
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>ผู้อำนวยการ (Director)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>เจ้าหน้าที่ (Staff)</option>
                    </select>
                </div>
            </div>

            {{-- Passwords Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        รหัสผ่าน <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           placeholder="••••••••"
                           class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                           required>
                </div>
                
                {{-- Confirm Password --}}
                <div>
                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                        ยืนยันรหัสผ่าน <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           placeholder="••••••••"
                           class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all" 
                           required>
                </div>
            </div>

            {{-- Submit & Cancel Buttons --}}
            <div class="pt-4 flex flex-col sm:flex-row items-center gap-3">
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-200 transition-all duration-200">
                    <i class="fa-solid fa-user-plus mr-2 text-xs"></i>
                    เพิ่มผู้ใช้งาน
                </button>
                <a href="{{ route('admin.users') }}" 
                   class="w-full sm:w-auto px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all text-center">
                    ยกเลิก
                </a>
            </div>
        </form>
    </div>
</div>
@endsection