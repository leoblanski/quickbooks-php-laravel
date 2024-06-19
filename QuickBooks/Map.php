<?php

/**
 *
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 *
 */

abstract class QuickBooks_Map
{
    public const MAP_QBXML = 'qbxml';

    public const MAP_IDS = 'ids';

    public const MARK_ADD = 'add';

    public const MARK_MOD = 'mod';

    public const MARK_DELETE = 'delete';

    abstract public function adds($adds = [], $mark_as_queued = true);

    abstract public function mods($mods = [], $mark_as_queued = true);

    abstract public function imports($imports = []);

    abstract public function queries($queries = []);

    abstract public function mark($mark_as, $object_or_action, $ID, $TxnID_or_ListID = null, $errnum = null, $errmsg = null);
}
