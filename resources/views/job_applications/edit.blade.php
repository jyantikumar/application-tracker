<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job Application</title>
</head>
<body>
    <h1>Edit Application</h1>

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $Error)
                <li>{{ $Error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('job-applications.update', $JobApplication->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Company Name:</label><br>
            <input type="text" name="company_name" value="{{ old('company_name', $JobApplication->company_name) }}" required>
        </div>
        <br>

        <div>
            <label>Position:</label><br>
            <input type="text" name="position" value="{{ old('position', $JobApplication->position) }}" required>
        </div>
        <br>

        <div>
            <label>Salary ($):</label><br>
            <input type="number" step="0.01" name="salary" value="{{ old('salary', $JobApplication->salary) }}" required>
        </div>
        <br>

        <div>
            <label>Job Type:</label><br>
            <select name="job_type" required>
                @foreach(['On-site', 'Remote', 'Hybrid'] as $Type)
                    <option value="{{ $Type }}" {{ old('job_type', $JobApplication->job_type) === $Type ? 'selected' : '' }}>
                        {{ $Type }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label>City:</label><br>
            <input type="text" name="city" value="{{ old('city', $JobApplication->city) }}" required>
        </div>
        <br>

        <div>
            <label>Application URL:</label><br>
            <input type="url" name="ApplicationURL" value="{{ old('ApplicationURL', $JobApplication->ApplicationURL) }}" required>
        </div>
        <br>

        <div>
            <label>Source:</label><br>
            <select name="source" required>
                @foreach(['LinkedIn', 'Indeed', 'Company Website', 'Referral'] as $SourceOption)
                    <option value="{{ $SourceOption }}" {{ old('source', $JobApplication->source) === $SourceOption ? 'selected' : '' }}>
                        {{ $SourceOption }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label>Status:</label><br>
            <select name="status" required>
                @foreach(['Applied', 'Interviewing', 'Offered', 'Rejected'] as $StatusOption)
                    <option value="{{ $StatusOption }}" {{ old('status', $JobApplication->status) === $StatusOption ? 'selected' : '' }}>
                        {{ $StatusOption }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label>Description:</label><br>
            <textarea name="description" rows="4" required>{{ old('description', $JobApplication->description) }}</textarea>
        </div>
        <br>

        <div>
            <label>Pros (Optional):</label><br>
            <input type="text" name="pros" value="{{ old('pros', $JobApplication->pros) }}">
        </div>
        <br>

        <div>
            <label>Cons (Optional):</label><br>
            <input type="text" name="cons" value="{{ old('cons', $JobApplication->cons) }}">
        </div>
        <br>

        <button type="submit">Update Application</button>
        <a href="{{ route('job-applications.index') }}">Cancel</a>
    </form>
</body>
</html>