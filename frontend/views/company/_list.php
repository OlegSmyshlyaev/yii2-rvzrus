<?php foreach ($company_list as $company) { ?>
    <li class="company-list-line__item wrap">
        <div class="company-list-line__staff">
            <div class="company-list-line__staff-image">
                <img class="company-list-line__staff-img" src="/frontend/assets/images/company/personals.png" />
            </div>
            <div class="company-list-line__staff-text"><?=$company['amount_staff'];?> чел.</div>
        </div>
        <div class="company-list-line__name">
            <a class="company-list-line__name-a" href="/company/<?=$company['company_id']?>"><?=$company['name_short']?></a>
        </div>
    </li>
<?php } ?>
