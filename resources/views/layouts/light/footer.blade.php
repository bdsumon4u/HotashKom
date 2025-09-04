<footer class="footer footer-fix">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6 footer-copyright">
        <p class="mb-0">Copyright {{date('Y')}} © All rights reserved.</p>
      </div>
      <div class="col-md-6">
        <p class="pull-right mb-0">Developed with <i class="fa fa-heart font-secondary"></i> <a href="{{$company->dev_link??'https://hotash.tech'}}" class="text-danger">{{$company->dev_name??'Hotash Tech'}}</a></p>
      </div>
    </div>
  </div>
</footer>
