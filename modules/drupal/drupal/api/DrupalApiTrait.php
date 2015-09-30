<?php

namespace atphp\module\drupal\drupal\api;

Trait DrupalApiTrait
{

    /** @var CacheAPI */
    private $drupalCache;

    /** @var EntityAPI */
    private $drupalEntity;

    /** @var PathAPI */
    private $drupalPath;

    /** @var ThemeAPI */
    private $drupalTheme;

    /** @var StringAPI */
    private $drupalString;

    /**
     * @return CacheAPI
     */
    public function getDrupalCache()
    {
        return $this->drupalCache;
    }

    /**
     * @param CacheAPI $drupalCache
     * @return self
     */
    public function setDrupalCache(CacheAPI $drupalCache)
    {
        $this->drupalCache = $drupalCache;
        return $this;
    }

    /**
     * @return DrupalEntity
     */
    public function getDrupalEntity()
    {
        return $this->drupalEntity;
    }

    /**
     * @param EntityAPI $drupalEntity
     * @return self
     */
    public function setDrupalEntity(EntityAPI $drupalEntity)
    {
        $this->drupalEntity = $drupalEntity;
        return $this;
    }

    /**
     * @return PathAPI
     */
    public function getDrupalPath()
    {
        return $this->drupalPath;
    }

    /**
     * @param PathAPI $drupalPath
     * @return self
     */
    public function setDrupalPath(PathAPI $drupalPath)
    {
        $this->drupalPath = $drupalPath;
        return $this;
    }

    /**
     * @return ThemeAPI
     */
    public function getDrupalTheme()
    {
        return $this->drupalTheme;
    }

    /**
     * @param ThemeAPI $drupalTheme
     * @return self
     */
    public function setDrupalTheme(ThemeAPI $drupalTheme)
    {
        $this->drupalTheme = $drupalTheme;
        return $this;
    }

    /**
     * @return StringAPI
     */
    public function getDrupalString()
    {
        return $this->drupalString;
    }

    /**
     * @param StringAPI $drupalString
     * @return self
     */
    public function setDrupalString(StringAPI $drupalString)
    {
        $this->drupalString = $drupalString;
        return $this;
    }

}
