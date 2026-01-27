<!DOCTYPE html>
<html>
<head>
    <title>Admin - User Pending</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #1f2937;
            color: white;
        }
        button {
            padding: 6px 12px;
            background: #16a34a;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Daftar User Menunggu Persetujuan</h2>

@if (session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->status }}</td>
                <td>
                    <form method="POST"
                          action="{{ route('admin.users.approve', $user->id) }}">
                        @csrf
                        <button type="submit">Approve</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Tidak ada user pending</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
