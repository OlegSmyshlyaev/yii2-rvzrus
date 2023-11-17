<?php 
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $meta_title;
?>
<div class="wrapper">
    <? /*
    <div class="head">
        <h2>ПКО 100: Профессионалы коллекторского бизнеса</h2>
        <div class="head__right-container">
            <img class="" src="" />
        </div>
    </div>
    */ ?>
    
    <div class="article_page">
        <h1>ПКО 300: Профессионалы коллекторского бизнеса</h1>

        <p>На этой странице представлена информация о ведущих игроках коллекторского рынка. Наша цель – дать вам подробный обзор деятельности этих компаний, помочь понять текущие тренды рынка и определить лидеров отрасли.</p>
        <h2>Что вы здесь найдете:</h2>
        <p><b>Рейтинг ПКО 300:</b> Этот индикатор основан на ссоотношении прибыли компании и количества ее сотрудников. Это отличный способ оценить не только финансовую надежность компании, но и ее общую производительность, качество управления и оптимизацию бизнес-процессов.</p>
        <p><b>Мощный инструмент фильтрации:</b> Позволяет вам легко сортировать список компаний по ключевым показателям, таким как прибыль, выручка, количество сотрудников или алфавиту.</p>
        <p><b>Подробные карточки компаний:</b> Здесь содержится всё, что вам нужно знать о каждой компании: от финансовых показателей до ссылок на их официальные сайты. Также указана информация об их членстве в НАПКА и статусе в реестре ФССП.</p>
        <p>Если вы заметили ошибки или хотите добавить информацию о своей компании, пишите нам на <a href="mailto:redchief@rvzrus.ru">redchief@rvzrus.ru</a>. Мы ценим ваш вклад в качество нашего ресурса и готовы улучшать его для вас.</p>
    </div>
    
    <div class="filter-container">
        <div class="filter-item">
            <div class="filter-item-content-wrapper ">
                <input id="company-filter-search" class="filter-item__search-input filter-item__input" name="company_search[request]" value="<?php echo $filter['search']['q']?>" /> 
                <div class="filter-item__search-submit-btn">
                    <input id="company-filter-search-submit" class="filter-item__search-submit-input" name="company_search[send]" type="submit" value="" />
                </div>
            </div>
        </div>
        <div class="filter-item">
            <div class="filter-item-content-wrapper ">
                <select id="company-filter-type" class="filter-item__type-select filter-item__select company-select" name="company_filter[type]">
                <?php foreach ($filter['type'] as $option) { ?>
                    <?php if ($option['default']) { ?>
                    <option value="<?=$option['value']?>" selected="selected"><?=$option['name']?></option>
                    <?php } else {?>
                    <option value="<?=$option['value']?>"><?=$option['name']?></option>
                    <?php } ?>
                <?php } ?>
                </select>
            </div>
        </div>
        <div class="filter-item">
            <div class="filter-item-content-wrapper ">
                <select id="company-filter-sort" class="filter-item__sort-select filter-item__select company-select" name="company_filter[sort]">
                <?php foreach ($filter['sort'] as $option) { ?>
                    <?php if ($option['default']) { ?>
                    <option value="<?=$option['value']?>" selected="selected"><?=$option['name']?></option>
                    <?php } else { ?>
                    <option value="<?=$option['value']?>"><?=$option['name']?></option>
                    <?php } ?>
                <?php } ?>
                </select>
            </div>
        </div>
    </div>
    
    <div class="clear"></div>
    
    <? if (!empty($company_list)) { ?>
    <?php
        if ($list) {
            $class = "list";
        } else {
            $class = "plate";
        }
    ?>
    <div class="company-list__conatainer <?=$class?> ajax-list-container">
        <?php if (!empty($company_view_switch)) { ?>
        <div class="company-view-switch__container">
        <?php foreach ($company_view_switch as $item) { ?>
            <?php if ($item['active']) { ?>
            <div class="company-view-switch__item"><span class="company-view-switch__item-a <?=$item['code']?> active"></span></div>
            <?php } else { ?>
            <div class="company-view-switch__item"><a class="company-view-switch__item-a <?=$item['code']?>" href="<?=$item['url']?>"></a></div>
            <?php } ?>
        <?php } ?>
        </div>
        <?php } ?>
        
        <div class="company-list__bottom">*данные ФНС, 2022</div>
        
        <?php if (!$list) { ?>
        <ul class="company-list__ul list ajax-list-content">
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
            <? } ?>
        </ul>
        <?php } else { ?>
        <ul class="company-list-line__ul ajax-list-content">
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
        </ul>
        <?php } ?>
        
        <div class="company-list__show" style="">
            <div class="company-list__show-more-wrapper">
            <?php if ($btn_show_more) { ?>
                <a class="company-list__show-more-btn ajax-list-show-more" href="#" data-offset="<?=count($company_list)?>">Показать еще</a>
            <?php } else { ?>
                <a class="company-list__show-more-btn ajax-list-show-more" href="#" data-offset="<?=count($company_list)?>" style="display: none;">Показать еще</a>
            <?php } ?>
            </div>
        </div>
        
        <div style="clear: both;"></div>
    </div>
    <? } ?>

</div>