<?php
/**
 * Class Paginator
 *
 * @copyright Copyright 2003-2015 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: $
 */
namespace ZenCart\Paginator;

/**
 * Class Paginator
 * @package ZenCart\Paginator
 */
class Paginator extends \base
{
    /**
     * @var mixed
     */
    protected $adapter;

    /**
     * @var mixed
     */
    protected $scroller;
    /**
     * @var array
     */
    protected $scrollerParams = array();
    /**
     * @var array
     */
    protected $adapterParams = array();

    /**
     * @var \ZenCart\Request\Request
     */
    protected $request;

    /**
     * @param \ZenCart\Request\Request $request
     */
    public function __construct(\ZenCart\Request\Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $adapterData
     * @param string $adapterType
     * @param string $scrollerType
     */
    public function doPagination($adapterData, $adapterType = 'QueryFactory', $scrollerType = 'Standard')
    {
        $mvcCmdName = issetorArray($this->scrollerParams, 'mvcCmdName', 'main_page');
        $this->scrollerParams['cmd'] = $this->request->readGet($mvcCmdName);
        $pagingVarName = issetorArray($this->scrollerParams, 'pagingVarName', 'page');
        $pagingVarSrc = issetorArray($this->scrollerParams, 'pagingVarSrc', 'get');
        $currentPage = $this->request->get($pagingVarName, 1, $pagingVarSrc);
        $this->adapterParams['currentPage'] = $currentPage;
        $this->scrollerParams['currentPage'] = $currentPage;
        $this->adapter = $this->buildAdapter($adapterType, $adapterData, $this->adapterParams);
        $this->scroller = $this->buildScroller($scrollerType, $this->adapter, $this->scrollerParams);
    }

    /**
     * @param string $adapterType
     * @param array $adapterData
     * @param array $adapterParams
     * @return mixed
     */
    protected function buildAdapter($adapterType, array $adapterData, array $adapterParams)
    {
        $className = __NAMESPACE__ . '\\adapters\\' . ucfirst($adapterType);
        $obj = new $className($adapterData, $adapterParams);
        return $obj;
    }

    /**
     * @param string $scrollerType
     * @param AdapterInterface $adapter
     * @param array $scrollerParams
     * @return mixed
     */
    protected function buildScroller($scrollerType, \ZenCart\Paginator\AdapterInterface $adapter, array $scrollerParams)
    {
        $className = __NAMESPACE__ . '\\scrollers\\' . ucfirst($scrollerType);
        $obj = new $className($adapter, $scrollerParams);
        return $obj;
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return mixed
     */
    public function getScroller()
    {
        return $this->scroller;
    }

    /**
     * @param $params
     */
    public function setScrollerParams($params)
    {
        $this->scrollerParams = array_merge($this->scrollerParams, $params);
    }

    public function setAdapterParams($params)
    {
        $this->adapterParams = array_merge($this->adapterParams, $params);
    }
}
