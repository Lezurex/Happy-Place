<?php

session_start();
session_unset();
session_abort();

header("Location: /admin?logout=1");