<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">File management</h1>
        </div>
        <div class="card-body">
            <div id="message" class="mb-3 text-center"></div>
            <div id="fileList">
                <ul class="list-group">
                    @foreach ($files as $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $file->id }}">
                            <div>
                                <a href="{{ asset('storage/' . $file->path) }}" target="_blank">{{ $file->file_name }}</a>
                                <span class="text-muted">({{ round($file->size / 1024, 2) }} KB)</span>
                            </div>
                            <button class="btn btn-danger btn-sm" onclick="deleteFile({{ $file->id }})">Delete</button>
                        </li>
                    @endforeach
                </ul>
            </div>
            <a href="/upload" class="btn btn-link mt-3">Return to upload</a>
        </div>
    </div>
</div>

<script>
    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file?')) return;

        const xhr = new XMLHttpRequest();
        xhr.open('DELETE', `/files/${fileId}`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

        xhr.onload = function() {
            const data = JSON.parse(xhr.responseText);
            const messageDiv = document.getElementById('message');
            if (xhr.status === 200 && data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                document.querySelector(`li[data-id="${fileId}"]`).remove();
            } else {
                messageDiv.innerHTML = '<div class="alert alert-danger">Delete error</div>';
            }
        };

        xhr.onerror = function() {
            document.getElementById('message').innerHTML = '<div class="alert alert-danger">Something went wrong</div>';
        };

        xhr.send();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
