PHP_ARG_ENABLE([couchbase],
               [whether to enable Couchbase support],
               [AS_HELP_STRING([--enable-couchbase],
                               [Enable Couchbase support])])

AC_SUBST(PHP_COUCHBASE)

if test "$PHP_COUCHBASE" != "no"; then
  PHP_REQUIRE_CXX
  AC_PATH_PROG(CMAKE, cmake, no)
  if ! test -x "${CMAKE}"; then
    AC_MSG_ERROR(Please install cmake to build couchbase extension)
  fi

  CXXFLAGS="${CXXFLAGS} -std=c++17"
  COUCHBASE_CMAKE_SOURCE_DIRECTORY="$srcdir/src"
  COUCHBASE_CMAKE_BUILD_DIRECTORY="$ac_pwd/cmake-build"

  PHP_SUBST([CMAKE])
  PHP_SUBST([COUCHBASE_CMAKE_SOURCE_DIRECTORY])
  PHP_SUBST([COUCHBASE_CMAKE_BUILD_DIRECTORY])

  PHP_ADD_LIBRARY_DEFER_WITH_PATH(couchbase_php_core, $EXTENSION_DIR, COUCHBASE_SHARED_LIBADD)
  PHP_ADD_LIBRARY_DEFER_WITH_PATH(couchbase_php_core, "$ac_pwd/modules", COUCHBASE_SHARED_LIBADD)

  PHP_SUBST([COUCHBASE_SHARED_LIBADD])
  COUCHBASE_SHARED_DEPENDENCIES="\$(phplibdir)/libcouchbase_php_core.${SHLIB_SUFFIX_NAME}"
  PHP_SUBST([COUCHBASE_SHARED_DEPENDENCIES])

  COUCHBASE_FILES="src/php_couchbase.cxx"

  PHP_NEW_EXTENSION(couchbase, ${COUCHBASE_FILES}, $ext_shared)
  PHP_ADD_EXTENSION_DEP(couchbase, json)
  PHP_ADD_BUILD_DIR($ext_builddir/src, 1)
fi

PHP_ADD_MAKEFILE_FRAGMENT

AC_CONFIG_COMMANDS_POST([
  echo "
CMAKE                  : ${CMAKE}
CMAKE_SOURCE_DIRECTORY : ${COUCHBASE_CMAKE_SOURCE_DIRECTORY}
CMAKE_BUILD_DIRECTORY  : ${COUCHBASE_CMAKE_BUILD_DIRECTORY}
CMAKE_C_COMPILER       : ${CC}
CMAKE_CXX_COMPILER     : ${CXX}
CMAKE_C_FLAGS          : ${CFLAGS}
CMAKE_CXX_FLAGS        : ${CXXFLAGS}
COUCHBASE_PHP_INCLUDES : ${INCLUDES}
COUCHBASE_PHP_LDFLAGS  : ${LDFLAGS}
COUCHBASE_PHP_LIBDIR   : ${phplibdir}
"
  ${CMAKE} -S ${COUCHBASE_CMAKE_SOURCE_DIRECTORY} -B${COUCHBASE_CMAKE_BUILD_DIRECTORY} \
         -DCMAKE_C_COMPILER="${CC}" \
         -DCMAKE_CXX_COMPILER="${CXX}" \
         -DCMAKE_C_FLAGS="${CFLAGS}" \
         -DCMAKE_CXX_FLAGS="${CXXFLAGS}" \
         -DCOUCHBASE_PHP_INCLUDES="${INCLUDES}" \
         -DCOUCHBASE_PHP_LDFLAGS="${LDFLAGS}" \
         -DCOUCHBASE_PHP_LIBDIR="${phplibdir}" \
         -DCOUCHBASE_CXX_CLIENT_BUILD_TESTS=OFF
])
