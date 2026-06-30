<?php $is_organization_page = true; ?>
<section class="content">
  <div class="container-fluid">

    <div class="card card-default">
      <div class="card card-primary card-outline">

        <div class="card-body">
          <table id="example1" data-empty-msg="ምንም መረጃ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>ስም</th>
                <th>ጾታ</th>
                <th>ቅርንጫፍ</th>
                <th class="text-center">ተግባር (Action)</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($awarenessList)): ?>
                <?php foreach ($awarenessList as $index => $awareness): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($awareness['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($awareness['sex'] ?? '') ?></td> 
                    <td><?= htmlspecialchars($awareness['branch_id'] ?? '') ?></td>
                    <td class="text-center">
                      <a href="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'], '/')) ?>/awareness-details/<?= (int)$awareness['tbleid'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                      </a>
                    </td>   
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center text-muted">ምንም የግንዛቤ ፈጠራ መረጃ አልተገኘም።</td>
                </tr>
              <?php endif; ?> </tbody> </table>

        </div> </div> </div> </div> </section>