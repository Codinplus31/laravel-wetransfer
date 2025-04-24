@component('mail::message')
# Your Files Have Been Uploaded

Your files have been successfully uploaded and are ready to be shared.

@component('mail::button', ['url' => $downloadLink])
Download Files
@endcomponent

**Details:**
- Expires at: {{ $uploadSession->expires_at->format('Y-m-d H:i:s') }}
- Number of files: {{ $uploadSession->files->count() }}
@if($uploadSession->password)
- This upload is password protected.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
