<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/hammer/hammer.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/sweetalert2/sweetalert2.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/toastr/toastr.js')) }}"></script>

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<script>
  $(document).on('click', '.btn-delete', function(req) {
    Swal.fire({
      title: '{{ __('menu.general.delete_confirm') }}',
      text: "{{ __('menu.general.delete_warning') }}",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#696cff',
      confirmButtonText: '{{ __('menu.general.delete') }}',
      cancelButtonText: '{{ __('menu.general.cancel') }}'
    }).then((result) => {
      if (result.isConfirmed) {
        $(this).parent('form').submit();
      }
    })
  });
</script>

<!-- Page JS -->
@stack('script')
<script>
  const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

  @if (session('success'))
  Toast.fire({
      icon: 'success',
      title: '{{ session('success') }}'
    })
  @elseif (session('error'))
    Toast.fire({
      icon: 'error',
      title: '{{ session('error') }}'
    })
  @elseif (session('info'))
    Toast.fire({
      icon: 'info',
      title: '{{ session('info') }}'
    })
  @endif
</script>

<!-- END: Page JS-->

@stack('modals')
@livewireScripts
<script src="{{ asset(mix('js/alpine.js')) }}"></script>
