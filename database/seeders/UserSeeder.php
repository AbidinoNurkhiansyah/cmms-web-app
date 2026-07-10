<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [1, 'Heri Purwanto', 'hericmms', 'zx2024', 'JID00849', 'Active', 'UNIT HEAD 1', 'TPM-OH-SM', 'TPM, Overhoul, SM-SP', NULL, User::ROLE_MANAGER],
            [2, 'Karsiwan', 'iwan', 'one', 'JID00008', 'Active', 'FUNCTION HEAD', 'MAINTENANCE', 'Manager', NULL, User::ROLE_MANAGER],
            [6, 'Yos Rizal', 'yos', '123', 'JID00151', 'Active', 'UNIT HEAD 1', 'MAINTENANCE', 'A/B', NULL, User::ROLE_TECHNICIAN],
            [8, 'Ali Rohman', 'ALI', '234', 'JID00195', 'Active', 'STL-2', 'TPM-OH', 'TPM, Overhoul', 'Ali Rohman', User::ROLE_TECHNICIAN],
            [9, 'Nawawi Santosa', 'Roeston CLBK', '1SAMPAI9', 'JID00196', 'Active', 'STL-1', 'MAINTENANCE A', 'A/B', 'Nawawi Santosa', User::ROLE_TECHNICIAN],
            [14, 'Rohmat', 'RHT', '525', 'JID00525', 'Active', 'SL-2', 'MAINTENANCE B', 'A/B', 'Rohmat', User::ROLE_TECHNICIAN],
            [15, 'Abdul Hambali', 'Abdul Hambali', 'JID00865', 'JID00865', 'Active', 'STL-1', 'MAINTENANCE', 'A/B', 'Abdul Hambali', User::ROLE_TECHNICIAN],
            [17, 'Anggi Arfian Dinata', 'anggi', 'mtc', 'JID01219', 'Active', 'SF-1', 'MAINTENANCE A', 'A/B', 'Anggi Arfian', User::ROLE_TECHNICIAN],
            [18, 'Arif Budi Wibowo', 'RadenMas', '681997', 'JID01268', 'Active', 'MOP', 'MAINTENANCE B', 'A/B', 'Arif Budi', User::ROLE_TECHNICIAN],
            [19, 'Rahmat Hidayat', 'rahmat', '11241', 'JID01497', 'Active', 'MOP', 'TPM', 'TPM', 'Rahmat Hidayat', User::ROLE_TECHNICIAN],
            [22, 'Istu Priyo Ari Widodo', 'istu', '0305', 'JID01727', 'Active', 'MOP', 'TPM', 'TPM', 'Istu Priyo', User::ROLE_TECHNICIAN],
            [23, 'Maryanto', 'mtc_anto', 'bebas', 'JID01807', 'Active', 'MOP', 'MAINTENANCE A', 'A/B', 'Maryanto', User::ROLE_TECHNICIAN],
            [24, 'Achmad Rofii', 'rofii', 'mtc', 'JID01811', 'Active', 'MOP', 'MAINTENANCE A', 'A/B', 'Achmad Rofii', User::ROLE_TECHNICIAN],
            [28, 'Ade Hartono', 'Ade', '101021', 'JID01968', 'Active', 'OP', 'MAINTENANCE A', 'A/B', 'Ade Hartono', User::ROLE_TECHNICIAN],
            [29, 'Asep Kurniawan', 'asep.mtc', 'Hino143143', 'JID01981', 'Active', 'MOP', 'MAINTENANCE B', 'A/B', 'Asep Kurniawan', User::ROLE_TECHNICIAN],
            [30, 'Muhamad Ilham Rudiantoro', 'Toro28', '123456', 'JID02187', 'Active', 'OP', 'MAINTENANCE B', 'A/B', 'Ilham Rudiantoro', User::ROLE_TECHNICIAN],
            [33, 'Bayu Adnan Ashari', 'bayu', '260124', 'JID02463', 'Active', 'OP', 'OH', 'Overhoul', 'Bayu Adnan', User::ROLE_TECHNICIAN],
            [34, 'Yayang Bachtiar', 'yayang', '12345', 'JID02589', 'Active', 'SL-1', 'TPM', 'TPM', 'Yayang Bachtiar', User::ROLE_TECHNICIAN],
            [35, 'Windra Aan Gunawan', 'WaanG', 'Ncepbudiman', 'JID02595', 'Active', 'OP', 'MAINTENANCE B', 'A/B', 'Windra Aan', User::ROLE_TECHNICIAN],
            [40, 'Widi Aryadi', 'widi', '02704', 'JID02704', 'Active', 'OP', 'OH', 'Overhoul', 'Widi Ariyadi', User::ROLE_TECHNICIAN],
            [41, 'Akhmad Iqbal Prasetyo', 'Iqbal', 'Iqbal204', 'JID02705', 'Active', 'OP', 'TPM', 'TPM', 'Akhmad Iqbal', User::ROLE_TECHNICIAN],
            [43, 'Aurora Firdaus Alalliy Wira Putri Nasution', 'Aurora', 'aurora11', 'JID02754', 'Active', 'CLERK-1', 'ADMIN', 'Admin', NULL, User::ROLE_OPERATOR],
            [44, 'hashimura', 'hashimura', '12345', null, 'Active', NULL, NULL, NULL, NULL, User::ROLE_TECHNICIAN],
            [45, 'prod', 'prod', '123', null, 'Active', NULL, NULL, NULL, NULL, User::ROLE_OPERATOR],
            [46, 'Khisio', 'kishio', '039002', null, 'Active', NULL, NULL, NULL, NULL, User::ROLE_TECHNICIAN],
            [47, 'Yuyun Sudiono', 'yuyun', 'yuno51', 'JID00051', 'Active', NULL, NULL, NULL, NULL, User::ROLE_TECHNICIAN],
            [48, 'cartam', 'cartam', '54321', 'JID00372', 'Active', 'ST-1', 'SUB MATERIAL', 'SM-SP', NULL, User::ROLE_TECHNICIAN],
            [49, 'Komarudin', 'komar', '212', 'JID01271', 'Active', 'CLERK-1', 'SUB MATERIAL', 'SM-SP', NULL, User::ROLE_TECHNICIAN]
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['username' => $u[2]],
                [
                    'name' => $u[1],
                    'password' => Hash::make($u[3]),
                    'email' => $u[2] . '@cmms.local',
                    'jid_no' => $u[4] === '' ? null : $u[4],
                    'status' => $u[5],
                    'position' => $u[6],
                    'team' => $u[7],
                    'jobdesc' => $u[8],
                    'repair' => $u[9],
                    'role' => $u[10]
                ]
            );
        }

        User::updateOrCreate(
            ['email' => 'superadmin@digimon.com'],
            [
                'name'     => 'Super Administrator',
                'username' => 'superadmin',
                'password' => Hash::make('password123'), // Password default
                'jid_no'   => 'JID99999',
                'position' => 'Manager',
                'team'     => 'MTC',
                'role'     => User::ROLE_MANAGER,
                'status'   => 'Active',
                'is_admin' => true,
            ]
        );

        // --- Dedicated Demo Accounts for Testing Each Role ---
        $demoAccounts = [
            ['Operator Demo', 'operator', User::ROLE_OPERATOR],
            ['Technician Demo', 'technician', User::ROLE_TECHNICIAN],
            ['Planner Demo', 'planner', User::ROLE_PLANNER],
            ['Supervisor Demo', 'supervisor', User::ROLE_SUPERVISOR],
            ['Manager Demo', 'manager', User::ROLE_MANAGER],
        ];

        foreach ($demoAccounts as $demo) {
            User::updateOrCreate(
                ['username' => $demo[1]],
                [
                    'name'     => $demo[0],
                    'password' => Hash::make('password123'), // Default password for all demo accounts
                    'email'    => $demo[1] . '@demo.com',
                    'jid_no'   => 'DEMO-' . strtoupper($demo[1]),
                    'position' => $demo[2],
                    'team'     => 'DEMO TEAM',
                    'role'     => $demo[2],
                    'status'   => 'Active',
                ]
            );
        }
    }
}
