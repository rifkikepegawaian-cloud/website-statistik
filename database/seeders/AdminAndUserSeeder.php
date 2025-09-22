<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAndUserSeeder extends Seeder {
  public function run(): void {
    User::updateOrCreate(
      ['username'=>'admin'],
      ['password'=>Hash::make('admin123'), 'role'=>'admin']
    );
    User::updateOrCreate(
      ['username'=>'operatorBPP'],
      ['password'=>Hash::make('OperatorBPP123'), 'role'=>'user']
    );
  }
}
