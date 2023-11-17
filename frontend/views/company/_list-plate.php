<?php foreach ($company_list as $company) { ?>
    <li class="company-item wrap">
        <div class="company-item__image" >
            <a class="company-item__image-a" href="/company/<?=$company['company_id']?>"><img class="company-item__image-img" src="<?=$company['image']?>" /></a>
        </div>
        <a class="company-item__name" href="/company/<?=$company['company_id']?>"><?=$company['name_short']?></a>
        <div class="company-item__website" ><a class="" href="<?=$company['website']?>"><?=$company['website']?></a></div>
        <div class="company-item__bottom-block" >
            <? if ($company['amount_staff']) {?>
            <div class="company-item__amount-personals" ><img class="company-item__personals-img" src="/frontend/assets/images/company/personals.png" /><?=$company['amount_staff'];?> чел.</div>
            <? } ?>
            <? if ($company['revenue_2019']) {?>
            <div class="company-item__proceeds" ><img class="company-item__proceeds-img" src="/frontend/assets/images/company/proffit.png" /> Выручка за год*: <?=number_format($company['revenue_2019'], 0, ".", " ")?> р.</div>
            <? } ?>
            <? if (is_numeric($company['rating']) && $company['rating'] !== 0) {?>
            <div class="" style="display: inline-block; height: 24px; line-height: 31px;">Рейтинг ПКО-300: <?=$company['rating']?></div>
            <? } ?>
        </div>
    </li>
<?php } ?>
