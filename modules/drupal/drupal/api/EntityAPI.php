<?php

namespace atphp\module\drupal\drupal\api;

class EntityAPI
{

    public function load($type, $id)
    {
        return entity_load_single($type, $id);
    }

    public function loadUnchanged($type, $id)
    {
        return entity_load_unchanged($type, $id);
    }

    public function loadMultiple($type, $ids = false, $conditions = [], $reset = false)
    {
        return entity_load($type, $ids, $conditions, $reset);
    }

    public function createStubEntity($type, $ids)
    {
        return entity_create_stub_entity($type, $ids);
    }

    public function extractIds($type, $entity)
    {
        return entity_extract_ids($type, $entity);
    }

    public function formSubmitBuildEntity($type, $entity, $form, &$form_state)
    {
        return entity_form_submit_build_entity($type, $entity, $form, $form_state);
    }

    public function getController($type)
    {
        return entity_get_controller($type);
    }

    public function getInfo($type = null)
    {
        return entity_get_info($type = null);
    }

    public function clearInfoCache()
    {
        return entity_info_cache_clear();
    }

    public function label($type, $entity)
    {
        return entity_label($type, $entity);
    }

    public function language($type, $entity)
    {
        return entity_language($type, $entity);
    }

    public function prepareView($type, $entities, $languageCode = null)
    {
        return entity_prepare_view($type, $entities, $languageCode);
    }

    public function uri($type, $entity)
    {
        return entity_uri($type, $entity);
    }

    public function viewModePrepare($type, $entities, $viewMode, $languageCode = null)
    {
        return entity_view_mode_prepare($type, $entities, $viewMode, $languageCode);
    }

}
