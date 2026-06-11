<div class="photo-box">
    @if($student->photo)
        <img src="{{ asset('storage/' . $student->photo) }}" alt="">
    @else
        <span class="photo-placeholder">
            {{ strtoupper(substr($student->last_name, 0, 1) . substr($student->first_name, 0, 1)) }}
        </span>
    @endif
</div>
