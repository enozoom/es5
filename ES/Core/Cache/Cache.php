<?php
namespace ES\Core\Cache;

use Psr\Cache\{CacheItemInterface,CacheItemPoolInterface,InvalidArgumentException};

class Cache implements CacheItemInterface, CacheItemPoolInterface,InvalidArgumentException
{

    /**
     * 返回当前缓存项的「键」
     *
     * 「键」由实现类库来加载，并且高层的调用者（如：CacheItemPoolInterface）
     *  **应该** 能使用此方法来获取到「键」的信息。
     *
     * @return string
     *   当前缓存项的「键」
     */
    public function getKey(){
        
    }

    /**
     * 凭借此缓存项的「键」从缓存系统里面取出缓存项。
     *
     * 取出的数据 **必须** 跟使用 `set()` 存进去的数据是一模一样的。
     *
     * 如果 `isHit()` 返回 false 的话，此方法必须返回 `null`，需要注意的是 `null`
     * 本来就是一个合法的缓存数据，所以你 **应该** 使用 `isHit()` 方法来辨别到底是
     * "返回 null 数据" 还是 "缓存里没有此数据"。
     *
     * @return mixed
     *   此缓存项的「键」对应的「值」，如果找不到的话，返回 `null`
     */
    public function get(){
        
    }

    /**
     * 确认缓存项的检查是否命中。
     *
     * 注意: 调用此方法和调用 `get()` 时 **一定不可** 有先后顺序之分。
     *
     * @return bool
     *   如果缓冲池里有命中的话，返回 `true`，反之返回 `false`
     */
    public function isHit(){}

    /**
     * 为此缓存项设置「值」。
     *
     * 参数 $value 可以是所有能被 PHP 序列化的数据，序列化的逻辑
     * 需要在实现类库里书写。
     *
     * @param mixed $value
     *   将被存储的可序列化的数据。
     *
     * @return static
     *   返回当前对象。
     */
    public function set($value){}

    /**
     * 设置缓存项的准确过期时间点。
     *
     * @param \\DateTimeInterface $expiration
     *
     *   过期的准确时间点，过了这个时间点后，缓存项就 **必须** 被认为是过期了的。
     *   如果明确的传参 `null` 的话，**可以** 使用一个默认的时间。
     *   如果没有设置的话，缓存 **应该** 存储到底层实现的最大允许时间。
     *
     * @return static
     *   返回当前对象。
     */
    public function expiresAt($expiration){}

    /**
     * 设置缓存项的过期时间。
     *
     * @param int|\\DateInterval $time
     *   以秒为单位的过期时长，过了这段时间后，缓存项就 **必须** 被认为是过期了的。
     *   如果明确的传参 `null` 的话，**可以** 使用一个默认的时间。
     *   如果没有设置的话，缓存 **应该** 存储到底层实现的最大允许时间。
     *
     * @return static
     *   返回当前对象
     */
    public function expiresAfter($time){}

    /**
     * 返回「键」对应的一个缓存项。
     *
     * 此方法 **必须** 返回一个 CacheItemInterface 对象，即使是找不到对应的缓存项
     * 也 **一定不可** 返回 `null`。
     *
     * @param string $key
     *   用来搜索缓存项的「键」。
     *
     * @throws InvalidArgumentException
     *   如果 $key 不是合法的值，\\Psr\\Cache\\InvalidArgumentException 异常会被抛出。
     *
     * @return CacheItemInterface
     *   对应的缓存项。
     */
    public function getItem($key){}
    
    /**
     * 返回一个可供遍历的缓存项集合。
     *
     * @param array $keys
     *   由一个或者多个「键」组成的数组。
     *
     * @throws InvalidArgumentException
     *   如果 $keys 里面有哪个「键」不是合法，\\Psr\\Cache\\InvalidArgumentException 异常
     *   会被抛出。
     *
     * @return array|\\Traversable
     *   返回一个可供遍历的缓存项集合，集合里每个元素的标识符由「键」组成，即使即使是找不到对
     *   的缓存项，也要返回一个「CacheItemInterface」对象到对应的「键」中。
     *   如果传参的数组为空，也需要返回一个空的可遍历的集合。
     */
    public function getItems(array $keys = array()){}
    
    /**
     * 检查缓存系统中是否有「键」对应的缓存项。
     *
     * 注意: 此方法应该调用 `CacheItemInterface::isHit()` 来做检查操作，而不是
     * `CacheItemInterface::get()`
     *
     * @param string $key
     *   用来搜索缓存项的「键」。
     *
     * @throws InvalidArgumentException
     *   如果 $key 不是合法的值，\\Psr\\Cache\\InvalidArgumentException 异常会被抛出。
     *
     * @return bool
     *   如果存在「键」对应的缓存项即返回 true，否则 false
     */
    public function hasItem($key){}
    
    /**
     * 清空缓冲池
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function clear(){}
    
    /**
     * 从缓冲池里移除某个缓存项
     *
     * @param string $key
     *   用来搜索缓存项的「键」。
     *
     * @throws InvalidArgumentException
     *   如果 $key 不是合法的值，\\Psr\\Cache\\InvalidArgumentException 异常会被抛出。
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function deleteItem($key){}
    
    /**
     * 从缓冲池里移除多个缓存项
     *
     * @param array $keys
     *   由一个或者多个「键」组成的数组。
     *
     * @throws InvalidArgumentException
     *   如果 $keys 里面有哪个「键」不是合法，\\Psr\\Cache\\InvalidArgumentException 异常
     *   会被抛出。
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function deleteItems(array $keys){}
    
    /**
     * 立刻为「CacheItemInterface」对象做数据持久化。
     *
     * @param CacheItemInterface $item
     *   将要被存储的缓存项
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function save(CacheItemInterface $item){}
    
    /**
     * 稍后为「CacheItemInterface」对象做数据持久化。
     *
     * @param CacheItemInterface $item
     *   将要被存储的缓存项
     *
     * @return bool
     *   成功返回 true，有错误发生返回 false
     */
    public function saveDeferred(CacheItemInterface $item){}
    
    /**
     * 提交所有的正在队列里等待的请求到数据持久层，配合 `saveDeferred()` 使用
     *
     * @return bool
     *  成功返回 true，有错误发生返回 false
     */
    public function commit(){}

    
}

