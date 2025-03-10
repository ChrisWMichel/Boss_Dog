@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
    <div class="flex items-center justify-center w-1/4">
        <a href="{{ route('dashboard') }}" class="block pt-2 pl-5"> 
          <img src="{{ asset('images/projectLogo-small.png') }}" alt="Logo" class="w-10 mx-auto" />
        </a>
      </div>
{{ $slot }}

</a>
</td>
</tr>
