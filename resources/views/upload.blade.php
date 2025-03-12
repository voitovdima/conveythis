<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload file</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">Upload file (PDF, DOCX)</h1>
        </div>
        <div class="card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="file" id="fileInput" accept=".pdf,.docx" class="form-control">
                </div>
                <button type="submit" class="btn btn-success w-100">Upload</button>
                <progress id="progressBar" value="0" max="100" class="w-100 mt-3 d-none"></progress>
            </form>
            <div id="message" class="mt-3 text-center"></div>
            <div id="fileList" class="mt-3">
                <h5>Last upload files</h5>
                <ul class="list-group">
                    @foreach (\App\Models\File::all()->take(5) as $file)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank">{{ $file->name }}</a>
                            <span>{{ round($file->size / 1024, 2) }} KB</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <a href="/files" class="btn btn-link mt-3">Manage files</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const fileInput = document.getElementById('fileInput');
        const progressBar = document.getElementById('progressBar');
        const messageDiv = document.getElementById('message');
        const fileList = document.getElementById('fileList').querySelector('ul');

        if (!fileInput.files[0]) {
            messageDiv.innerHTML = '<div class="alert alert-danger">Choose a file!</div>';
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload-file', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                progressBar.classList.remove('d-none');
                progressBar.value = percent;
            }
        };

        xhr.onload = function() {
            progressBar.classList.add('d-none');
            const data = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `<a href="/storage/uploads/${data.name}" target="_blank">${data.name}</a>`;
                fileList.prepend(li);
                fileInput.value = '';
            } else {
                messageDiv.innerHTML = '<div class="alert alert-danger">Upload error</div>';
            }
        };

        xhr.onerror = function() {
            progressBar.classList.add('d-none');
            messageDiv.innerHTML = '<div class="alert alert-danger">Something went wrong</div>';
        };

        xhr.send(formData);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
