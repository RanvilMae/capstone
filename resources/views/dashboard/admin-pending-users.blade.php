<h2>Pending Users</h2>
<table class="table-auto w-full">
<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
@foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>
            <form action="{{ route('admin.approve-user', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded">Approve</button>
            </form>
        </td>
    </tr>
@endforeach
</tbody>
</table>
